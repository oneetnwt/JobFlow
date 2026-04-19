<?php

namespace App\Enums;

enum PlanFeature: string
{
    case CUSTOM_BRANDING = 'custom_branding';
    case ADVANCED_REPORTS = 'advanced_reports';
    case API_ACCESS = 'api_access';
    case WEBHOOK = 'webhook';
    case PRIORITY_SUPPORT = 'priority_support';
    case UNLIMITED_USERS = 'unlimited_users';
    case UNLIMITED_JOBS = 'unlimited_job_orders';
    case CUSTOM_DOMAIN = 'custom_domain';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
