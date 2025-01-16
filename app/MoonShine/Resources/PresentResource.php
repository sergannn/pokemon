<?php

namespace App\MoonShine\Resources;

use App\Models\Present;
use App\Models\Marker;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Fields\Image;
use MoonShine\Resources\ModelResource;

use MoonShine\Fields\Relationships\BelongsTo;

use Sweet1s\MoonshineRBAC\Traits\WithPermissionsFormComponent;
use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;
class PresentResource extends ModelResource
{
   // use WithRolePermissions;
   // use WithPermissionsFormComponent;
    protected string $model = Present::class;

    protected string $title = 'Призы';

    protected string $column = 'name';

    protected bool $stickyTable = true;

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Block::make('', [
                        ID::make()->sortable(),
                        Text::make('Название','title'), 
                        Text::make('Цена','price'),
                        Image::make( 'Изображение', 'img' ),
                        BelongsTo::make('Маркер', 'marker', 'title', new MarkerResource())->nullable()
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
