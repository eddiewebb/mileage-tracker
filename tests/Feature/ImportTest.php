<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test import page is accessible to authenticated users.
     */
    public function test_import_page_is_accessible_to_authenticated_users()
    {
        $response = $this->actingAs($this->user)->get('/import');

        $response->assertStatus(200);
        $response->assertSee('Import Trips');
    }

    /**
     * Test CSV file upload and preview functionality.
     */
    public function test_csv_file_upload_and_preview_functionality()
    {
        // Create a mock CSV file
        $csvContent = "Date,Start,End,Mileage,Purpose,Notes,Tags\n";
        $csvContent .= "2024-01-15,\"123 Main St, CA\",\"456 Business Ave, CA\",25.5,\"Client meeting\",\"Important meeting\",\"client,meeting\"\n";
        $csvContent .= "2024-01-16,\"456 Business Ave, CA\",\"789 Office Blvd, CA\",18.7,\"Return to office\",\"\",\"office\"";

        $file = UploadedFile::fake()->createWithContent('trips.csv', $csvContent);

        $response = $this->actingAs($this->user)->post('/import/preview', [
            'csv_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Import Preview');
        $response->assertSee('123 Main St, CA');
        $response->assertSee('25.5');
    }

    /**
     * Test CSV validation catches invalid records.
     */
    public function test_csv_validation_catches_invalid_records()
    {
        // Create a CSV with invalid data
        $csvContent = "Date,Start,End,Mileage,Purpose,Notes,Tags\n";
        $csvContent .= "invalid-date,\"123 Main St, CA\",\"456 Business Ave, CA\",25.5,\"Client meeting\",\"Notes\",\"client\"\n";
        $csvContent .= "2024-01-16,\"\",\"456 Business Ave, CA\",invalid-mileage,\"Purpose\",\"Notes\",\"office\"";

        $file = UploadedFile::fake()->createWithContent('invalid-trips.csv', $csvContent);

        $response = $this->actingAs($this->user)->post('/import/preview', [
            'csv_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Import Errors');
        $response->assertSee('Invalid date format');
    }

    /**
     * Test import confirmation creates trips.
     */
    public function test_import_confirmation_creates_trips()
    {
        $tripData = [
            'trips' => [
                [
                    'date' => '2024-01-15',
                    'start' => '123 Main St, CA',
                    'end' => '456 Business Ave, CA',
                    'mileage' => 25.5,
                    'purpose' => 'Client meeting',
                    'notes' => 'Important client',
                    'tags' => ['client', 'meeting'],
                ],
                [
                    'date' => '2024-01-16',
                    'start' => '456 Business Ave, CA',
                    'end' => '789 Office Blvd, CA',
                    'mileage' => 18.7,
                    'purpose' => 'Return to office',
                    'notes' => '',
                    'tags' => ['office'],
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/import/confirm', $tripData);

        $response->assertRedirect('/trips');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trips', [
            'user_id' => $this->user->id,
            'start_location' => '123 Main St, CA',
            'end_location' => '456 Business Ave, CA',
            'mileage' => 25.5,
        ]);

        $this->assertDatabaseHas('trips', [
            'user_id' => $this->user->id,
            'start_location' => '456 Business Ave, CA',
            'end_location' => '789 Office Blvd, CA',
            'mileage' => 18.7,
        ]);
    }

    /**
     * Test import creates labels automatically.
     */
    public function test_import_creates_labels_automatically()
    {
        $tripData = [
            'trips' => [
                [
                    'date' => '2024-01-15',
                    'start' => '123 Main St, CA',
                    'end' => '456 Business Ave, CA',
                    'mileage' => 25.5,
                    'purpose' => 'Client meeting',
                    'notes' => '',
                    'tags' => ['new-label', 'another-label'],
                ],
            ],
        ];

        $this->actingAs($this->user)->post('/import/confirm', $tripData);

        $this->assertDatabaseHas('labels', [
            'user_id' => $this->user->id,
            'name' => 'new-label',
        ]);

        $this->assertDatabaseHas('labels', [
            'user_id' => $this->user->id,
            'name' => 'another-label',
        ]);
    }

    /**
     * Test file upload validation.
     */
    public function test_file_upload_validation()
    {
        // Test with non-CSV file
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user)->post('/import/preview', [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
    }

    /**
     * Test large file upload rejection.
     */
    public function test_large_file_upload_rejection()
    {
        // Create a file larger than 2MB
        $file = UploadedFile::fake()->create('large-file.csv', 3000); // 3MB

        $response = $this->actingAs($this->user)->post('/import/preview', [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
    }
}
