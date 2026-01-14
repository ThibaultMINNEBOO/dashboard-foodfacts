<?php

namespace App\Domain\Enums;

enum WidgetType: string
{
    case PRODUCT_COUNT_BY_COUNTRIES = 'product_count_by_countries';
    case PRODUCT_COUNT_BY_NUTRISCORE = 'product_count_by_nutriscore';
    case PRODUCT_COUNT = 'product_count';
}
