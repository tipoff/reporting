<?php

declare(strict_types=1);

namespace Tipoff\Reviews\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Tipoff\Support\Nova\BaseResource;

class Review extends BaseResource
{
    public static $model = \Tipoff\Reviews\Models\Review::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static $group = 'Reporting';

    public function fieldsForIndex(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Location', 'location', nova('location'))->sortable(),
            DateTime::make('Review Date', 'reviewed_at')->format('YYYY-MM-DD')->sortable(),
            Number::make('Rating')->sortable(),
            Badge::make('Replied', function () {
                if (isset($this->replied_at)) {
                    return 'Yes';
                }

                return 'No';
            })->map([
                'No' => 'danger',
                'Yes' => 'success',
            ]),
            Boolean::make('Displayed', 'is_displayable')->sortable(),
        ];
    }

    public function fields(Request $request)
    {
        return [
            Number::make('Rating')->readonly(),
            Textarea::make('Review Comment', 'comment')->rows(3)->alwaysShow()->readonly(),
            Text::make('Reviewer')->readonly(),
            Image::make('Reviewer Photo')->preview(function ($value) {
                return $value;
            })->readonly(),
            DateTime::make('Review Date', 'reviewed_at')->readonly(),
            BelongsTo::make('Location', 'location', nova('location'))->readonly(),

            new Panel('Display Details', $this->displayFields()),

            new Panel('Reply Details', $this->replyFields()),

            new Panel('Identifier Data', $this->dataFields()),
        ];
    }

    protected function displayFields()
    {
        return [
            Boolean::make('Displayed', 'is_displayable'),
            Boolean::make('Photo Displayed', 'is_photo_displayable'),
            Text::make('Display Reviewer Name', 'reviewer_displayable')->nullable(),
            Text::make('Display Title', 'title_displayable')->nullable(),
            Markdown::make('Display Comment', 'comment_displayable')->alwaysShow()->nullable(),
        ];
    }

    protected function replyFields()
    {
        return [
            Badge::make('Replied', function () {
                if (isset($this->replied_at)) {
                    return 'Yes';
                }

                return 'No';
            })->map([
                'No' => 'danger',
                'Yes' => 'success',
            ])->hideWhenUpdating(),
            Textarea::make('Review Reply', 'reply')->rows(3)->alwaysShow()->hideWhenUpdating(),
            DateTime::make('Reply Date', 'replied_at')->hideWhenUpdating(),
        ];
    }

    protected function dataFields()
    {
        return [
            ID::make()->hideWhenUpdating(),
            Text::make('Google Review ID', 'google_ref')->hideWhenUpdating(),
            DateTime::make('Created At')->exceptOnForms(),
            DateTime::make('Updated At')->exceptOnForms(),
        ];
    }

    public function cards(Request $request)
    {
        return [];
    }

    public function filters(Request $request)
    {
        return [
            // TODO - figure out how to share this
            // new Filters\Location,
        ];
    }

    public function lenses(Request $request)
    {
        return [];
    }

    public function actions(Request $request)
    {
        return [];
    }
}
