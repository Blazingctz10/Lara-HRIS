<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\Widget;

class PortalShortcutWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.portal-shortcut-widget';

    protected static bool $isLazy = false;

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function getData(): array
    {
        return [];
    }
}
