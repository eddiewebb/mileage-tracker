# Mobile Mileage Tracker

A responsive webapp that allows business miles to be tracked and reported according to IRS regulations.

## Requirements
1) Require authorization to login and access any data
2) All trip data will be restricted to the creating user account
3) The system must have a mobile friendly and responsive layout

## Scenarios
1) As an uninitialized system, when a user first visits the app, then they will be prompted to create an account with email and password.
1) As an authenticated user, when I search for a starting location or an ending location, the address will auto-complete based on user entry.
2) Once both addresses are entered, then I am shown a map confirming my route and total mileage.
3) As an authenticated user, I can define the IRS rate per mile, per calendar year, defaulting to published IRS rate for current year. The mileage rate must be consistent across all trips within a calendar year, but will change year to year. Updating the mileage rate in current or previous years will not affect any other years.
4) As an authenticated user, when I confirm the route is correct, then I will be able to enter notes on the trip's purpose, and tag it with a common set of labels along with the date and time of travel. Existing tags should auto-complete as I type.
5) As an authenticated user, when I attempt to use a label that does not exist then it will be added.
6) As an authenticated user, when I view the reports area, then I am prompted for a date range and report format which may be PDF, or csv.
7) As the system, when provided with a date range and report format I will report on all logged trips in that period including the start, end, mileage, date, tags, notes, and IRS rated cost.
8) As an authenticated user, when I need to import existing trips from another system, then I can provide a csv file with dates, start, end, mileage, notes, and tags. The system will prompt me to review it's parsed information, and then record all trips under the appropriate time period and mileage rates.

## Language
The responsive webapp must use a PHP backend with a suitable modern mvc framework. The system must support an sqlite or mysql database for data retention. 