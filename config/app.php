<?php

use Illuminate\Support\Facades\Facade;

return [

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        // 'Form' => Collective\Html\FormFacade::class,
        // 'Html' => Collective\Html\HtmlFacade::class,
        'Html' => Spatie\Html\Facades\Html::class,
        'Helper' => App\Helpers\Helper::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    ])->toArray(),

];
