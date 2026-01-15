<?php

namespace App\Filament\Newsletter\Resources\Subscribers\Pages;

use App\Filament\Newsletter\Resources\Subscribers\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Modules\Newsletter\Models\Subscriber;
use App\Modules\Newsletter\Models\Tag;
use App\Models\Guest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('importGuests')
                    ->label('Import Guests from Letter System')
                    ->tooltip('Import all guests from Guest Letter system that are not already in subscribers')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->requiresConfirmation()
                    ->modalHeading('Import Guests from Letter System')
                    ->modalDescription('This will import all guests from the Guest Letter system that are not already in subscribers.')
                    ->action(function () {
                        // Get all guests
                        $guests = Guest::all();

                        $imported = 0;
                        $skipped = 0;

                        foreach ($guests as $guest) {
                            // Check if email already exists in subscribers
                            $exists = Subscriber::where('email', $guest->email)->exists();

                            if (!$exists) {
                                Subscriber::create([
                                    'email' => $guest->email,
                                    'name' => $guest->full_name,
                                    'country_code' => $guest->country,
                                    'birth_date' => $guest->birth_date,
                                    'is_active' => true,
                                ]);
                                $imported++;
                            } else {
                                $skipped++;
                            }
                        }

                        Notification::make()
                            ->title('Import complete')
                            ->success()
                            ->body("Imported: $imported guests\nSkipped: $skipped (already exist)")
                            ->send();
                    }),

                Action::make('import')
                    ->label('Import Subscribers from CSV')
                    ->tooltip('Import subscriber list from a CSV file with optional tag assignment')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Import Subscribers')
                    ->modalWidth('lg')
                    ->form([
                        Select::make('tag_id')
                            ->label('Tag (optional)')
                            ->options(Tag::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->placeholder('No tag'),

                        FileUpload::make('file')
                            ->label('CSV File')
                            ->required()
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->disk('local')
                            ->directory('imports')
                            ->visibility('private')
                            ->helperText('email,birth_date,country_code,name,is_active,unsubscribe_token'),
                    ])

                    ->action(function (array $data) {
                        $path = Storage::path($data['file']);
                        $handle = fopen($path, 'r');

                        $row = 0;
                        $emails = [];
                        $countriesUsed = []; // country_code => true

                        $countryNames = [
                            'ALB' => 'Albania',
                            'DZA' => 'Algeria',
                            'AND' => 'Andorra',
                            'AGO' => 'Angola',
                            'AIA' => 'Anguilla',
                            'ATG' => 'Antigua and Barbuda',
                            'ARG' => 'Argentina',
                            'ARM' => 'Armenia',
                            'AUS' => 'Australia',
                            'AUT' => 'Austria',
                            'AZE' => 'Azerbaijan',
                            'BHS' => 'Bahamas',
                            'BHR' => 'Bahrain',
                            'BGD' => 'Bangladesh',
                            'BRB' => 'Barbados',
                            'BLR' => 'Belarus',
                            'BEL' => 'Belgium',
                            'BLZ' => 'Belize',
                            'BEN' => 'Benin',
                            'BTN' => 'Bhutan',
                            'BOL' => 'Bolivia',
                            'BIH' => 'Bosnia and Herzegovina',
                            'BWA' => 'Botswana',
                            'BRA' => 'Brazil',
                            'VGB' => 'British Virgin Islands',
                            'BRN' => 'Brunei',
                            'BGR' => 'Bulgaria',
                            'BFA' => 'Burkina Faso',
                            'BDI' => 'Burundi',
                            'KHM' => 'Cambodia',
                            'CMR' => 'Cameroon',
                            'CAN' => 'Canada',
                            'CPV' => 'Cape Verde',
                            'CYM' => 'Cayman Islands',
                            'CAF' => 'Central African Republic',
                            'TCD' => 'Chad',
                            'CHL' => 'Chile',
                            'CHN' => 'China',
                            'CXR' => 'Christmas Island',
                            'CCK' => 'Cocos Islands',
                            'COL' => 'Colombia',
                            'COM' => 'Comoros',
                            'COG' => 'Congo',
                            'CRI' => 'Costa Rica',
                            'HRV' => 'Croatia',
                            'CUB' => 'Cuba',
                            'CYP' => 'Cyprus',
                            'CZE' => 'Czech Republic',
                            'DNK' => 'Denmark',
                            'DMA' => 'Dominica',
                            'DOM' => 'Dominican Republic',
                            'ECU' => 'Ecuador',
                            'EGY' => 'Egypt',
                            'SLV' => 'El Salvador',
                            'GNQ' => 'Equatorial Guinea',
                            'ERI' => 'Eritrea',
                            'EST' => 'Estonia',
                            'ETH' => 'Ethiopia',
                            'FRO' => 'Faroe Islands',
                            'FJI' => 'Fiji',
                            'FIN' => 'Finland',
                            'FRA' => 'France',
                            'GAB' => 'Gabon',
                            'GMB' => 'Gambia',
                            'GEO' => 'Georgia',
                            'DEU' => 'Germany',
                            'GHA' => 'Ghana',
                            'GIB' => 'Gibraltar',
                            'GRC' => 'Greece',
                            'GRL' => 'Greenland',
                            'GRD' => 'Grenada',
                            'GUM' => 'Guam',
                            'GTM' => 'Guatemala',
                            'GGY' => 'Guernsey',
                            'GIN' => 'Guinea',
                            'GNB' => 'Guinea-Bissau',
                            'GUY' => 'Guyana',
                            'HTI' => 'Haiti',
                            'HND' => 'Honduras',
                            'HKG' => 'Hong Kong',
                            'HUN' => 'Hungary',
                            'ISL' => 'Iceland',
                            'IDN' => 'Indonesia',
                            'IND' => 'India',
                            'IRN' => 'Iran',
                            'IRQ' => 'Iraq',
                            'IRL' => 'Ireland',
                            'IMN' => 'Isle of Man',
                            'ISR' => 'Israel',
                            'ITA' => 'Italy',
                            'JAM' => 'Jamaica',
                            'JPN' => 'Japan',
                            'JEY' => 'Jersey',
                            'JOR' => 'Jordan',
                            'KAZ' => 'Kazakhstan',
                            'KEN' => 'Kenya',
                            'KIR' => 'Kiribati',
                            'KWT' => 'Kuwait',
                            'KGZ' => 'Kyrgyzstan',
                            'LAO' => 'Laos',
                            'LVA' => 'Latvia',
                            'LBN' => 'Lebanon',
                            'LSO' => 'Lesotho',
                            'LBR' => 'Liberia',
                            'LBY' => 'Libya',
                            'LIE' => 'Liechtenstein',
                            'LTU' => 'Lithuania',
                            'LUX' => 'Luxembourg',
                            'MAC' => 'Macau',
                            'MWI' => 'Malawi',
                            'MYS' => 'Malaysia',
                            'MDV' => 'Maldives',
                            'MLI' => 'Mali',
                            'MLT' => 'Malta',
                            'MHL' => 'Marshall Islands',
                            'MRT' => 'Mauritania',
                            'MUS' => 'Mauritius',
                            'MYT' => 'Mayotte',
                            'MEX' => 'Mexico',
                            'FSM' => 'Micronesia',
                            'MDA' => 'Moldova',
                            'MCO' => 'Monaco',
                            'MNG' => 'Mongolia',
                            'MNE' => 'Montenegro',
                            'MSR' => 'Montserrat',
                            'MAR' => 'Morocco',
                            'MOZ' => 'Mozambique',
                            'MMR' => 'Myanmar',
                            'NAM' => 'Namibia',
                            'NRU' => 'Nauru',
                            'NPL' => 'Nepal',
                            'NLD' => 'Netherlands',
                            'NZL' => 'New Zealand',
                            'NIC' => 'Nicaragua',
                            'NER' => 'Niger',
                            'NGA' => 'Nigeria',
                            'MKD' => 'North Macedonia',
                            'PRK' => 'North Korea',
                            'NOR' => 'Norway',
                            'OMN' => 'Oman',
                            'PAK' => 'Pakistan',
                            'PLW' => 'Palau',
                            'PAN' => 'Panama',
                            'PNG' => 'Papua New Guinea',
                            'PRY' => 'Paraguay',
                            'PER' => 'Peru',
                            'PHL' => 'Philippines',
                            'POL' => 'Poland',
                            'PRT' => 'Portugal',
                            'QAT' => 'Qatar',
                            'REU' => 'Réunion',
                            'ROU' => 'Romania',
                            'RUS' => 'Russia',
                            'RWA' => 'Rwanda',
                            'KNA' => 'Saint Kitts and Nevis',
                            'LCA' => 'Saint Lucia',
                            'VCT' => 'Saint Vincent and the Grenadines',
                            'WSM' => 'Samoa',
                            'SMR' => 'San Marino',
                            'STP' => 'São Tomé and Príncipe',
                            'SAU' => 'Saudi Arabia',
                            'SEN' => 'Senegal',
                            'SRB' => 'Serbia',
                            'SYC' => 'Seychelles',
                            'SLE' => 'Sierra Leone',
                            'SGP' => 'Singapore',
                            'SVK' => 'Slovakia',
                            'SVN' => 'Slovenia',
                            'SLB' => 'Solomon Islands',
                            'SOM' => 'Somalia',
                            'ZAF' => 'South Africa',
                            'KOR' => 'South Korea',
                            'SSD' => 'South Sudan',
                            'ESP' => 'Spain',
                            'LKA' => 'Sri Lanka',
                            'SDN' => 'Sudan',
                            'SUR' => 'Suriname',
                            'SWZ' => 'Eswatini',
                            'SWE' => 'Sweden',
                            'CHE' => 'Switzerland',
                            'SYR' => 'Syria',
                            'TWN' => 'Taiwan',
                            'TJK' => 'Tajikistan',
                            'TZA' => 'Tanzania',
                            'THA' => 'Thailand',
                            'TLS' => 'Timor-Leste',
                            'TGO' => 'Togo',
                            'TON' => 'Tonga',
                            'TTO' => 'Trinidad and Tobago',
                            'TUN' => 'Tunisia',
                            'TUR' => 'Turkey',
                            'TKM' => 'Turkmenistan',
                            'TCA' => 'Turks and Caicos Islands',
                            'TUV' => 'Tuvalu',
                            'UGA' => 'Uganda',
                            'UKR' => 'Ukraine',
                            'ARE' => 'United Arab Emirates',
                            'GBR' => 'United Kingdom',
                            'USA' => 'United States',
                            'VIR' => 'U.S. Virgin Islands',
                            'URY' => 'Uruguay',
                            'UZB' => 'Uzbekistan',
                            'VUT' => 'Vanuatu',
                            'VAT' => 'Vatican City',
                            'VEN' => 'Venezuela',
                            'VNM' => 'Vietnam',
                            'WLF' => 'Wallis and Futuna',
                            'ESH' => 'Western Sahara',
                            'YEM' => 'Yemen',
                            'ZMB' => 'Zambia',
                            'ZWE' => 'Zimbabwe',
                        ];
                        while (($csv = fgetcsv($handle, 2000, ',')) !== false) {
                            $row++;

                            // header
                            if ($row === 1) {
                                continue;
                            }

                            // CSV: email,birth_date,country_code,name,is_active,unsubscribe_token
                            $email            = strtolower(trim($csv[0] ?? ''));
                            $birthDate        = trim($csv[1] ?? '');
                            $countryCode      = strtoupper(trim($csv[2] ?? ''));
                            $name             = trim($csv[3] ?? '');
                            $isActive         = (int) ($csv[4] ?? 1);
                            $unsubscribeToken = trim($csv[5] ?? '');

                            if (! $email) {
                                continue;
                            }

                            $emails[] = $email;

                            if ($countryCode !== '') {
                                $countriesUsed[$countryCode] = true;
                            }

                            Subscriber::updateOrCreate(
                                ['email' => $email],
                                [
                                    'name'              => $name,
                                    'birth_date'        => $birthDate ?: null,
                                    'country_code'      => $countryCode ?: null,
                                    'is_active'         => $isActive === 1,
                                    'unsubscribe_token' => $unsubscribeToken ?: Str::random(32),
                                ]
                            );
                        }

                        fclose($handle);

                        $emails = array_values(array_unique($emails));

                        // 1) Attach selected tag (manual)
                        if (! empty($data['tag_id'])) {
                            Subscriber::whereIn('email', $emails)
                                ->select('id')
                                ->chunkById(500, function ($subs) use ($data) {
                                    foreach ($subs as $sub) {
                                        $sub->tags()->syncWithoutDetaching([$data['tag_id']]);
                                    }
                                });
                        }

                        // 2) Auto-create + attach FULL country name tags
                        $tagIdsByCountry = [];

                        foreach (array_keys($countriesUsed) as $code) {
                            $countryName = $countryNames[$code] ?? $code; // fallback to code if unknown
                            $slug = 'country-' . Str::slug($countryName);

                            $tag = Tag::firstOrCreate(
                                ['slug' => $slug],
                                ['name' => 'Country ' . $countryName]
                            );

                            $tagIdsByCountry[$code] = $tag->id;
                        }

                        Subscriber::whereIn('email', $emails)
                            ->select('id', 'country_code')
                            ->chunkById(500, function ($subs) use ($tagIdsByCountry) {
                                foreach ($subs as $sub) {
                                    $code = strtoupper((string) $sub->country_code);

                                    if ($code && isset($tagIdsByCountry[$code])) {
                                        $sub->tags()->syncWithoutDetaching([$tagIdsByCountry[$code]]);
                                    }
                                }
                            });
                    })->successNotificationTitle('Subscribers imported successfully'),

                Action::make('deduplicate')
                    ->label('Check & Merge Duplicates')
                    ->tooltip('Find and merge duplicate subscriber emails')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function () {
                        $duplicates = \App\Modules\Newsletter\Services\SubscriberDeduplicationService::findDuplicates();

                        if (empty($duplicates)) {
                            Notification::make()
                                ->title('No Duplicates Found')
                                ->body('All subscriber emails are unique!')
                                ->success()
                                ->send();
                            return;
                        }

                        $stats = \App\Modules\Newsletter\Services\SubscriberDeduplicationService::mergeAllDuplicates();

                        Notification::make()
                            ->title('Deduplication Complete')
                            ->body(
                                'Found: ' . $stats['total_duplicates_found'] . ' duplicate emails. ' .
                                    'Merged: ' . (count($stats['merged_emails']) - $stats['error_count']) . ' successfully. ' .
                                    'Errors: ' . $stats['error_count']
                            )
                            ->success()
                            ->send();
                    }),

                Action::make('purgeBouncedCsv')
                    ->label('Purge Bounced CSV')
                    ->tooltip('Remove bounced email addresses from the database using a CSV file')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->modalHeading('Purge bounced emails from database')
                    ->modalDescription('Step 1: Upload CSV to preview. Step 2: Type DELETE to permanently remove matching subscribers.')
                    ->modalWidth('lg')
                    ->form([
                        FileUpload::make('file')
                            ->label('Bounce CSV File')
                            ->required()
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->disk('local')
                            ->directory('imports')
                            ->visibility('private')
                            ->helperText('CSV must contain an "email" column OR emails in the first column.'),

                        Select::make('mode')
                            ->label('Mode')
                            ->options([
                                'dry_run' => 'Dry run (preview only)',
                                'delete'  => 'Delete from database',
                            ])
                            ->default('dry_run')
                            ->required()
                            ->live(),

                        TextInput::make('confirm')
                            ->label('Type DELETE to confirm')
                            ->placeholder('DELETE')
                            ->helperText('Required only when Mode = Delete')
                            ->visible(fn(callable $get) => $get('mode') === 'delete'),
                    ])
                    ->action(function (array $data) {
                        $mode = $data['mode'] ?? 'dry_run';

                        // If delete mode, require typing DELETE
                        if ($mode === 'delete') {
                            $confirm = strtoupper(trim((string) ($data['confirm'] ?? '')));
                            if ($confirm !== 'DELETE') {
                                Notification::make()
                                    ->title('Confirmation required')
                                    ->danger()
                                    ->body('Type DELETE to proceed.')
                                    ->send();
                                return;
                            }
                        }

                        $path = Storage::path($data['file']);

                        $handle = fopen($path, 'r');
                        if (!$handle) {
                            Notification::make()->title('Failed to open CSV file')->danger()->send();
                            return;
                        }

                        $firstRow = fgetcsv($handle, 5000, ',');
                        if ($firstRow === false) {
                            fclose($handle);
                            Notification::make()->title('CSV file is empty')->danger()->send();
                            return;
                        }

                        $firstRowLower = array_map(fn($v) => strtolower(trim((string) $v)), $firstRow);
                        $emailIndex = array_search('email', $firstRowLower, true);

                        $emails = [];

                        $pushEmail = function (?string $value) use (&$emails) {
                            $email = strtolower(trim((string) $value));
                            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $emails[] = $email;
                            }
                        };

                        if ($emailIndex !== false) {
                            // header mode
                            while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                                $pushEmail($row[$emailIndex] ?? null);
                            }
                        } else {
                            // first column mode (first row is data)
                            $pushEmail($firstRow[0] ?? null);
                            while (($row = fgetcsv($handle, 5000, ',')) !== false) {
                                $pushEmail($row[0] ?? null);
                            }
                        }

                        fclose($handle);

                        $emails = array_values(array_unique($emails));

                        if (count($emails) === 0) {
                            Notification::make()
                                ->title('No valid emails found in CSV')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Count how many exist in DB (chunk to avoid SQL limits)
                        $existsCount = 0;
                        foreach (array_chunk($emails, 1000) as $chunk) {
                            $existsCount += Subscriber::whereIn('email', $chunk)->count();
                        }

                        // Dry run: show preview only
                        if ($mode === 'dry_run') {
                            $sample = array_slice($emails, 0, 15);
                            $body = "CSV emails: " . count($emails)
                                . "\nFound in database: " . $existsCount
                                . "\n\nSample:\n" . implode("\n", $sample)
                                . (count($emails) > 15 ? "\n... +" . (count($emails) - 15) . " more" : '');

                            Notification::make()
                                ->title('Dry run complete')
                                ->success()
                                ->body($body)
                                ->send();

                            return;
                        }

                        // Delete mode
                        $deletedTotal = 0;
                        foreach (array_chunk($emails, 1000) as $chunk) {
                            $deletedTotal += Subscriber::whereIn('email', $chunk)->delete();
                        }

                        Notification::make()
                            ->title('Purge complete')
                            ->success()
                            ->body("CSV emails: " . count($emails) . "\nMatched in database: " . $existsCount . "\nDeleted: " . $deletedTotal)
                            ->send();
                    }),
            ])
                ->label('Tools')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info'),

            Actions\CreateAction::make(),
        ];
    }
}
