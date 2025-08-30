<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Label;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test label creation with find or create method.
     */
    public function test_label_creation_with_find_or_create_method()
    {
        $user = User::factory()->create();
        
        $label = Label::findOrCreateForUser($user->id, 'test-label');

        $this->assertInstanceOf(Label::class, $label);
        $this->assertEquals('test-label', $label->name);
        $this->assertEquals($user->id, $label->user_id);
        $this->assertNotNull($label->color);
    }

    /**
     * Test existing label is returned instead of creating duplicate.
     */
    public function test_existing_label_is_returned_instead_of_creating_duplicate()
    {
        $user = User::factory()->create();
        
        // Create initial label
        $existingLabel = Label::create([
            'user_id' => $user->id,
            'name' => 'existing-label',
            'color' => '#FF0000',
        ]);

        // Try to find or create the same label
        $foundLabel = Label::findOrCreateForUser($user->id, 'existing-label');

        $this->assertEquals($existingLabel->id, $foundLabel->id);
        $this->assertEquals('#FF0000', $foundLabel->color); // Should keep existing color
    }

    /**
     * Test labels are user-specific.
     */
    public function test_labels_are_user_specific()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $label1 = Label::findOrCreateForUser($user1->id, 'same-name');
        $label2 = Label::findOrCreateForUser($user2->id, 'same-name');

        $this->assertNotEquals($label1->id, $label2->id);
        $this->assertEquals($user1->id, $label1->user_id);
        $this->assertEquals($user2->id, $label2->user_id);
    }

    /**
     * Test random color generation produces valid hex colors.
     */
    public function test_random_color_generation_produces_valid_hex_colors()
    {
        $user = User::factory()->create();
        
        $label = Label::findOrCreateForUser($user->id, 'color-test');

        $this->assertMatchesRegularExpression('/^#[A-Fa-f0-9]{6}$/', $label->color);
    }

    /**
     * Test label relationships with user.
     */
    public function test_label_relationships_with_user()
    {
        $user = User::factory()->create();
        $label = Label::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $label->user->id);
        $this->assertTrue($user->labels->contains($label));
    }
}
