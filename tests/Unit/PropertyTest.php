<?php

namespace Jeffpereira\RealEstate\Tests\Unit;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Jeffpereira\RealEstate\Http\Requests\Property\BusinessRequest;
use Jeffpereira\RealEstate\Models\Property\Business;
use Jeffpereira\RealEstate\Models\Property\Property;
use Jeffpereira\RealEstate\Models\Property\SubType;
use Jeffpereira\RealEstate\Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_slug_must_be_unique()
    {
        $property = factory(Property::class)->create();
        $codeException = null;
        try {
            factory(Property::class)->create(['slug' => $property->slug]);
        } catch (\Illuminate\Database\QueryException $ex) {
            $codeException = $ex->getCode();
        }
        $this->assertEquals('23000', $codeException);
    }

    public function test_format_generate_slug_based_in_context()
    {
        $address = $this->createAddress();
        $business = factory(Business::class)->create();
        $subType = factory(SubType::class)->create();
        $property = Property::create([
            'sub_type_id' => $subType->id, 'business_id' => $business->id,
            'address_id' => $address->id, 'min_description' => "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Magni necessitatibus non architecto adipisci quidem",
            'max_dormitory' => 3, 'max_bathroom' => 2, 'max_suite' => 1, 'max_garage' => 2
        ]);
        $this->assertEquals(
            Str::slug(
                Str::limit(
                    sprintf(
                        "%s %s em %s - %s %s %s %s %s",
                        $property->business->name,
                        Str::title($property->sub_type->name),
                        Str::title($property->address->neighborhood->name),
                        Str::title($property->address->neighborhood->city->state->name),
                        $property->max_dormitory ? "$property->max_dormitory dormitório(s)," : '',
                        $property->max_bathroom ? "$property->max_bathroom banheiro(s)," : '',
                        $property->max_suite ? "$property->max_suite suite(s)," : '',
                        $property->max_garage ? "$property->max_garage garagem(ns)," : '',
                    ),
                    150
                )
            ),
            $property->slug
        );
    }
}