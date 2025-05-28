<?php

namespace App\Livewire\Tables;

use App\Models\Patient;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PatientsTable extends PowerGridComponent
{
    public string $tableName = 'patients-table-bmidob-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->includeViewOnTop('patients.header-patients-table'),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Patient::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('first_name')
            ->add('last_name')
            ->add('email')
            ->add('phone')
            ->add('dob')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [

            Column::make('Nombres', 'first_name')
                ->sortable()
                ->searchable(),
            Column::make('Apellidos', 'last_name')
                ->sortable()
                ->searchable(),
            Column::make('Correo', 'email')
                ->sortable()
                ->searchable(),
            Column::make('Celular', 'phone')
                ->sortable()
                ->searchable(),
            Column::make('Fecha de nacimiento', 'dob')
                ->sortable()
                ->searchable(),

            Column::make('Fecha de creación', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Opciones')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Patient $row): array
    {
        return [
            Button::add('edit')
                ->slot('
                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-7 h-7 me-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 21h17v-2H4v2zm3-4.5 10-10L15.5 4 5.5 14l-1.5 4 4-1.5z"/>
                </svg>
            ')
                ->class('text-basic hover:text-black')

        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
