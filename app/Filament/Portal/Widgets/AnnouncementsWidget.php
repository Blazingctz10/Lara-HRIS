<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class AnnouncementsWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.announcements-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getAnnouncements()
    {
        return Announcement::accessibleBy()
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 1 WHEN priority = 'important' THEN 2 ELSE 3 END")
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();
    }
}
