<?php

namespace App\MoonShine\Resources;


use App\Models\Marker;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;

//use Sweet1s\MoonshineRBAC\Traits\WithPermissionsFormComponent;
//use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;
class MarkerResource extends ModelResource
{
   // use WithRolePermissions;
   // use WithPermissionsFormComponent;
    protected string $model = Marker::class;
    protected array $with = ['user'];
    protected string $title = 'Маркеры';

    protected string $column = 'name';

    protected bool $stickyTable = true;

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Block::make('', [
                        ID::make()->sortable(),
                        Text::make("Название","title"),
                        Text::make('Широта (lat)','lat1'),
                        Text::make('Долгота (lon)','lon1'),
                        Text::make('Широта (lat)','lat2'),
                        Text::make('Долгота (lon)','lon2'),
                        Text::make('Широта (lat)','lat3'),
                        Text::make('Долгота (lon)','lon3'),
                        Text::make('Широта (lat)','lat4'),
                        Text::make('Долгота (lon)','lon4'),
                        Text::make('Статус','status'),
                        BelongsTo::make('Приз', 'present', 'title', new PresentResource())->nullable(),
                        BelongsTo::make('User', 'user',   'id',resource:new UserResource()),
//                        resource:new MoonShineUserResource(),
//                            fn($item) => "$item->id. $item->title") 
//                        ->nullable(),
                      
                       // Select::make('user','user'), 
                       // BelongsTo::make('Создатель','user',resource:new MoonShineUserResource())
                    //  fn($item) => "$item->id. $item->title") 
                      //  ->nullable(),
                        //Switcher::make('роль доступна для регистрации','description')->hideOnIndex(),
                    ]),
                    LineBreak::make(),
                ]),
            ]),
        ];
    }

    public function rules(Model $item): array
    {
        return [
     //       'name' => 'required',
     //       'email' => 'sometimes|bail|required|email|unique:users,email' . ($item->exists ? ",$item->id" : ''),
     //       'password' => ! $item->exists
     //           ? 'required|min:6|required_with:password_repeat|same:password_repeat'
     //           : 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }
}
