<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Author')
                    ->default(fn () => auth()->id())
                    ->searchable()
                    ->required(),
                TextInput::make('title')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('slug')
                    ->maxLength(255)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique URL name, for example: my-first-post')
                    ->required(),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Textarea::make('excerpt')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->required()
                    ->rows(16)
                    ->helperText('Plain text; line breaks are preserved.')
                    ->columnSpanFull(),
                FileUpload::make('featured_image')
                    ->image()
                    ->disk('public')
                    ->directory('blog')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_published')
                    ->default(false)
                    ->live(),
                DateTimePicker::make('published_at')
                    ->default(now())
                    ->requiredIf('is_published', true)
                    ->helperText('Published posts become visible at this time. Future dates schedule publication.'),
            ]);
    }
}
