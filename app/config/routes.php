<?php declare(strict_types=1);
return [
            '~^user(/page-(?<page>\d+))?$'      => '#user->index',
            '~^user/(?<login>\w+)$'             => '#user->profile',

            '~^api/user/(?<login>\w+)$'         => "#api@profile",
            
            '~^blog/archive/(?<year>\d+)$'      =>"#blog@archive_yearly",
            '~^blog/archive/(?<year>\d+)-(?<month>\d+)(/page(?<page>\d+))?$'    =>"#blog@archive_monthly",
            '~^blog/tag/(?<label>\w+)(/page(?<page>\d+))?$'                     =>"#blog@tag",
            '~^blog/page/(?<slug>\S+)$'                                         =>"#blog@post",
            '~^blog(/(?<id>\d+))?$'                                              =>"#blog@index",
        ];
