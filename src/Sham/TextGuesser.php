<?php

namespace Trellis\Sham;

use Faker\Generator;

/**
 * Generates text data by guessing suitable content based on a given property/attribute name.
 */
class TextGuesser
{
    protected static $generator_map = null;

    /**
     * @param string $name name to use for guessing a provider.
     * @param Generator $generator instance with fake data providers to use for fake data generation
     *
     * @return mixed || null if guessing failed
     */
    public static function guess($name, Generator $generator)
    {
        $map_key = mb_strtolower($name);
        $generator_map = self::getGeneratorMap($generator);

        if (isset($generator_map[$map_key])) {
            return $generator_map[$map_key]();
        }

        return null;
    }

    protected static function getGeneratorMap(Generator $generator)
    {
        if (!is_array(self::$generator_map)) {
            self::$generator_map = array_merge(
                self::buildFirstnameMap($generator),
                self::buildLastnameMap($generator),
                self::buildNameMap($generator),
                self::buildUsernameMap($generator),
                self::buildEmailMap($generator),
                self::buildDateMap($generator),
                self::buildPhoneMap($generator),
                self::buildAddressMap($generator),
                self::buildCityMap($generator),
                self::buildStreetAddressMap($generator),
                self::buildHousenumberMap($generator),
                self::buildPostcodeMap($generator),
                self::buildCountryMap($generator),
                self::buildFederalStateMap($generator),
                self::buildTextMap($generator),
                self::buildUrlMap($generator),
                self::buildLongitudeMap($generator),
                self::buildLatitudeMap($generator)
            );
        }

        return self::$generator_map;
    }

    protected static function buildFirstnameMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'first_name',
                'firstname',
                'given_name',
                'givenname'
            ],
            function () use ($generator) {
                return $generator->firstName;
            }
        );
    }

    protected static function buildLastnameMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'last_name',
                'lastname',
                'surname',
                'family_name',
                'familyname'
            ],
            function () use ($generator) {
                return $generator->lastName;
            }
        );
    }

    protected static function buildUsernameMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'alias',
                'moniker',
                'handle',
                'username',
                'user_name',
                'login',
                'login_name',
                'nick',
                'nickname',
                'nick_name'
            ],
            function () use ($generator) {
                return $generator->userName;
            }
        );
    }

    protected static function buildDateMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'iso6801',
                'birthdate',
                'birthday',
                'datetime',
                'date',
                'updated_at',
                'inserted_at',
                'created_at',
                'deleted_at'
            ],
            function () use ($generator) {
                return $generator->iso8601;
            }
        );
    }

    protected static function buildPhoneMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'phone',
                'fax',
                'telefax',
                'telephone',
                'telefon',
                'phone_number',
                'phonenumber',
                'phoneno',
                'phone_no',
                'phone_num',
                'mobile',
                'mobile_phone',
                'mobile_no',
                'mobile_num',
                'cellphone',
                'cell_phone',
                'cell_no',
                'cell_num',
                'cellular',
                'cellular_phone'
            ],
            function () use ($generator) {
                return $generator->phoneNumber;
            }
        );
    }

    protected static function buildEmailMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'email',
                'e_mail',
                'e-mail',
                'email_address',
                'emailaddress'
            ],
            function () use ($generator) {
                return $generator->email;
            }
        );
    }

    protected static function buildNameMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'name',
                'full_name',
                'fullname',
                'author',
                'creator',
                'composer',
                'editor',
                'user',
                'writer',
                'novelist',
                'financier',
                'participant',
                'inviter',
                'invitee',
                'attender',
                'attendee',
                'attendant',
                'partner',
                'accomplice',
                'witness',
                'assistant',
                'aide',
                'helper',
                'associate',
                'colleague',
                'cohort',
                'fellow',
                'worker',
                'coworker',
                'co_worker',
                'employer',
                'employee',
                'manager',
                'boss',
                'principal',
                'head',
                'leader',
                'contributor',
                'donor',
                'spender',
                'sponsor',
                'benefactor',
                'presenter',
                'anchorman',
                'anchorwoman',
                'anchor',
                'moderator',
                'host',
                'co_host',
                'cohost',
                'tv_host',
                'tvhost',
                'quizmaster'
            ],
            function () use ($generator) {
                return $generator->name;
            }
        );
    }

    protected static function buildAddressMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'address',
                'adress'
            ],
            function () use ($generator) {
                return $generator->address;
            }
        );
    }

    protected static function buildCityMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'city',
                'town'
            ],
            function () use ($generator) {
                return $generator->city;
            }
        );
    }

    protected static function buildStreetAddressMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'streetaddress',
                'street_address',
                'street',
                'street_number',
                'road'
            ],
            function () use ($generator) {
                return $generator->streetAddress;
            }
        );
    }

    protected static function buildHousenumberMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'house_number',
                'housenumber',
                'building_number',
                'buildingnumber'
            ],
            function () use ($generator) {
                return $generator->buildingNumber;
            }
        );
    }

    protected static function buildPostcodeMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'post_code',
                'postal_code',
                'postal_areacode',
                'postal_area_code',
                'postal_address',
                'zip_code',
                'zipcode',
                'zip'
            ],
            function () use ($generator) {
                return $generator->postcode;
            }
        );
    }

    protected static function buildCountryMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'country',
                'nation'
            ],
            function () use ($generator) {
                return $generator->country;
            }
        );
    }

    protected static function buildFederalStateMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'state',
                'federal_state',
                'federalstate',
                'federate_state',
                'federatestate',
                'province'
            ],
            function () use ($generator) {
                return $generator->state;
            }
        );
    }

    protected static function buildTextMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'title',
                'headline',
                'subheadline',
                'content'
            ],
            function () use ($generator) {
                return $generator->sentence;
            }
        );
    }

    protected static function buildUrlMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'url',
                'website',
                'web',
                'homepage',
                'link'
            ],
            function () use ($generator) {
                return $generator->url;
            }
        );
    }

    protected static function buildLatitudeMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'lat',
                'latitude'
            ],
            function () use ($generator) {
                return $generator->latitude;
            }
        );
    }

    protected static function buildLongitudeMap(Generator $generator)
    {
        return array_fill_keys(
            [
                'lon',
                'lng',
                'longitude'
            ],
            function () use ($generator) {
                return $generator->longitude;
            }
        );
    }
}
