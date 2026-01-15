<?php

namespace App\Filament\GuestLetter\Resources\Bookings\Schemas;

use App\Models\Guest;
use App\Models\Room;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;


class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('guest_id')
                ->label('Guest')
                ->placeholder('Select guest')
                ->options(
                    Guest::query()
                        ->orderBy('first_name')
                        ->get()
                        ->mapWithKeys(fn($g) => [
                            $g->id => trim("{$g->title} {$g->first_name} {$g->last_name}") . " ({$g->email})",
                        ])
                        ->toArray()
                )
                ->searchable()
                ->required()
                ->createOptionForm([
                    Grid::make(2)
                        ->components([
                            Select::make('title')
                                ->label('Title')
                                ->placeholder('Select an option')
                                ->options([
                                    'Mr.'   => 'Mr.',
                                    'Mrs.'  => 'Mrs.',
                                    'Ms.'   => 'Ms.',
                                    'Miss'  => 'Miss',
                                    'Dr.'   => 'Dr.',
                                    'Prof.' => 'Prof.',
                                    'Mx.'   => 'Mx.',
                                ])
                                ->required(),

                            TextInput::make('first_name')
                                ->label('First name')
                                ->required()
                                ->maxLength(120),

                            TextInput::make('last_name')
                                ->label('Last name')
                                ->maxLength(120),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(190),

                            TextInput::make('phone')
                                ->label('Phone')
                                ->tel()
                                ->maxLength(50),

                            Select::make('country')
                                ->label('Country')
                                ->placeholder('Select country')
                                ->options(self::countries())
                                ->searchable(),

                            DatePicker::make('birth_date')
                                ->label('Birth date')
                                ->native(false)
                                // keep on left column like your screenshot
                                ->columnSpan(1),
                        ])
                ])
                ->createOptionUsing(fn(array $data) => Guest::create($data)->id),

            TextInput::make('booking_number')
                ->label('Booking number')
                ->required()
                // Prevent duplicate booking numbers
                ->unique(table: 'gl_bookings', column: 'booking_number', ignoreRecord: true)
                ->maxLength(80),

            DatePicker::make('arrival_date')
                ->label('Arrival date')
                ->required()
                ->native(false),

            DatePicker::make('departure_date')
                ->label('Departure date')
                ->required()
                ->native(false),

            TextInput::make('adult')
                ->label('Adult')
                ->numeric()
                ->minValue(1)
                ->default(2)
                ->required(),

            TextInput::make('child')
                ->label('Child')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),

            Select::make('room_id')
                ->label('Room')
                ->placeholder('Select room')
                ->options(
                    Room::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->required(),

            // TextInput::make('campaign_name')
            //     ->label('Campaign name')
            //     ->maxLength(150),

            // RichEditor::make('campaign_benefit')
            //     ->label('Campaign benefit')
            //     ->columnSpanFull()
            //     ->extraInputAttributes(['class' => 'min-h-[300px] p-4 leading-relaxed']),

            // RichEditor::make('remark')
            //     ->label('Remark')
            //     ->columnSpanFull()
            //     ->extraInputAttributes(['class' => 'min-h-[250px] p-4 leading-relaxed']),
        ])->columns(2);
    }

    private static function countries(): array
    {
        return [
            'AF' => 'Afghanistan',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BR' => 'Brazil',
            'BN' => 'Brunei',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CR' => 'Costa Rica',
            'CI' => "Côte d'Ivoire",
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GR' => 'Greece',
            'GD' => 'Grenada',
            'GT' => 'Guatemala',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macau',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'KP' => 'North Korea',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'QA' => 'Qatar',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'KR' => 'South Korea',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syria',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Vatican City',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ];
    }
}
