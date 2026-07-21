<?php

namespace App\Observers;

use App\Models\PageContent;

class PageContentObserver
{
    public function saved(PageContent $pageContent): void
    {
        PageContent::forgetCache($pageContent->page);
    }

    public function deleted(PageContent $pageContent): void
    {
        PageContent::forgetCache($pageContent->page);
    }
}
