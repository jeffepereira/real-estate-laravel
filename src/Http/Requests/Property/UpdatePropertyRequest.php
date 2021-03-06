<?php

namespace Jeffpereira\RealEstate\Http\Requests\Property;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jeffpereira\RealEstate\Models\Property\Property;

class UpdatePropertyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => ['sometimes', 'bail', 'slug', 'min:3', 'max:150', Rule::unique('properties')->ignore($this->property)],
            'code' => ['sometimes', 'bail', 'integer', Rule::unique('properties')->ignore($this->property)],
            'businesses.*.id' => 'sometimes|bail|required|uuid',
            'businesses.*.value' => 'sometimes|bail|nullable|numeric|between:0,99999999.99',
            'situation_id' => 'sometimes|bail|nullable|uuid',
            'sub_type_id' => 'sometimes|bail|required|uuid',
            'min_description' => 'sometimes|bail|nullable|min:10|max:200',
            'total_area' => 'sometimes|bail|nullable|numeric|between:0,99999999.99',
            'building_area' => 'sometimes|bail|nullable|numeric|between:0,99999999.99',
            'min_dormitory' => 'sometimes|bail|nullable|integer|min:0|lte:max_dormitory',
            'max_dormitory' => 'sometimes|bail|nullable|integer|min:0',
            'min_suite' => 'sometimes|bail|nullable|integer|min:0|lte:max_suite',
            'max_suite' => 'sometimes|bail|nullable|integer|min:0',
            'min_bathroom' => 'sometimes|bail|nullable|integer|min:0|lte:max_bathroom',
            'max_bathroom' => 'sometimes|bail|nullable|integer|min:0',
            'min_garage' => 'sometimes|bail|nullable|integer|min:0|lte:max_garage',
            'max_garage' => 'sometimes|bail|nullable|integer|min:0',
            'embed' => 'sometimes|bail|nullable|url|max:300',
            'address' => 'sometimes|bail|nullable|max:100',
            'number' => 'sometimes|nullable|integer|min:o',
            'complement' => 'max:15',
            'cep' => 'sometimes|bail|nullable|formato_cep',
            'latitude' => 'sometimes|nullable|integer',
            'longitude' => 'sometimes|nullable|integer',
            'neighborhood' => 'bail|required|min:2|max:100',
            'city' => 'bail|required|min:2|max:100',
            'state' => 'bail|min:2|max:100',
            'initials' => 'bail|required|min:2|max:2',
        ];
    }

    public function messages()
    {
        return [
            'lte' => 'O valor m??nimo deve ser menor ou igual ao m??ximo.',
            'min' => 'O campo deve ter no m??nimo :min caracteres.',
            'max' => 'Limite o campo a no m??ximo :max caracteres.',
            'numeric' => 'Formato de n??mero inv??lido.',
            'integer' => 'Forne??a um n??mero inteiro.',
            'between' => 'Faixa de valores dispon??veis: 0 - 99999999.99.',
            'slug' => 'Formato de slug inv??lido. Tente algo parecido com formato-de-slug-correto-99.',
            'url' => 'Formato de url inv??lido.',
            'code.integer' => 'Formato de c??digo inv??lido.',
            'code.unique' => 'Este c??digo j?? est?? sendo usado por outro im??vel.',
            'business_id.uuid' => 'Escolha um tipo de neg??cio.',
            'address_id.required' => 'Cadastre um endere??o.',
            'address_id.uuid' => 'Cadastre um endere??o.',
            'sub_type_id.required' => 'Escolha um tipo de im??vel.',
            'sub_type_id.uuid' => 'Escolha um tipo de im??vel.',
            'slug.unique' => 'J?? existe um im??vel com este slug.',
            'min_dormitory.min' => 'Forne??a um n??mero inteiro.',
            'min_suite.min' => 'Forne??a um n??mero inteiro.',
            'min_bathroom.min' => 'Forne??a um n??mero inteiro.',
            'min_garage.min' => 'Forne??a um n??mero inteiro.',
            'number.min' => 'Forne??a um n??mero maior do que 0.',
            'businesses.*.id' => [
                'required' => 'Escolha um tipo de neg??cio',
                'uuid' => 'Escolha um tipo de neg??cio',
            ],
            'businesses.*.value' => [
                'between' => 'Faixa de valores dispon??veis: 0 - 99999999.99.',
                'uuid' => 'Escolha um tipo de neg??cio',
            ],
            // Address
            'neighborhood.required' => 'Forne??a um bairro.',
            'city.required' => 'Forne??a uma cidade.',
            'initials.required' => 'Forne??a um estado.',
        ];
    }

    public function getData(): array
    {
        return $this->except(
            array_merge(
                ['businesses'],
                Property::$columnsAddress,
                Property::$columnsCreateUpdateNeighborhood
            )
        );
    }

    public function prepareForValidation()
    {
        $this->merge([
            'useful_area' => $this->useful_area ? $this->useful_area : null,
            'building_area' => $this->building_area ? $this->building_area : null,
            'total_area' => $this->total_area ? $this->total_area : null,
            'number' => $this->not_number ? null : $this->number,
            'neighborhood' => Str::upper($this->neighborhood),
            'city' => Str::upper($this->city),
            'state' => $this->state ? Str::upper($this->state) : $this->getStateByInitials($this->initials),
            'initials' => Str::upper($this->initials),
            'country' => $this->country ? Str::upper($this->country) : 'BRASIL',
        ]);
    }

    private function getStateByInitials($initials): string
    {
        $initials = Str::upper($initials);
        $states = [
            ['AC' => 'Acre'],
            ['AL' => 'Alagoas'],
            ['AP' => 'Amap??'],
            ['AM' => 'Amazonas'],
            ['BA' => 'Bahia'],
            ['CE' => 'Cear??'],
            ['DF' => 'Distrito Federal'],
            ['ES' => 'Esp??rito Santo'],
            ['GO' => 'Goi??s'],
            ['MA' => 'Maranh??o'],
            ['MT' => 'Mato Grosso'],
            ['MS' => 'Mato Grosso do Sul'],
            ['MG' => 'Minas Gerais'],
            ['PA' => 'Par??'],
            ['PB' => 'Para??ba'],
            ['PR' => 'Paran??'],
            ['PE' => 'Pernambuco'],
            ['PI' => 'Piau??'],
            ['RJ' => 'Rio de Janeiro'],
            ['RN' => 'Rio Grande do Norte'],
            ['RS' => 'Rio Grande do Sul'],
            ['RO' => 'Rond??nia'],
            ['RR' => 'Roraima'],
            ['SC' => 'Santa Catarina'],
            ['SP' => 'S??o Paulo'],
            ['SE' => 'Sergipe'],
            ['TO' => 'Tocantins'],
        ];

        return isset($states[$initials]) ? Str::upper($states[$initials]) : 'UNDEFINED';
    }
}
