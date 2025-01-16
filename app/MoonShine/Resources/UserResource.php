<?php

namespace App\MoonShine\Resources;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;

//use Sweet1s\MoonshineRBAC\Traits\WithPermissionsFormComponent;
//use Sweet1s\MoonshineRBAC\Traits\WithRolePermissions;
class UserResource extends ModelResource
{
   // use WithRolePermissions;
   // use WithPermissionsFormComponent;
    protected string $model = User::class;
    protected array $with = ['presents'];
    protected string $title = 'Юзеры';

    protected string $column = 'name';

    protected bool $stickyTable = true;

    public function fields(): array
    {
        return [
            Grid::make([
                Column::make([
                    Block::make('', [
                        ID::make()->sortable(),
                        Text::make('Попытки',"attempts"),
                        HasMany::make('Маркеры', 'markers','id',resource: new MarkerResource()),
                       // HasMany::make('Призы', 'presents','id',resource: new PresentResource())
                       // BelongsTo::make('User', 'user',   fn($item)=> 1 ,resource:new MoonShineUserResource()),
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
