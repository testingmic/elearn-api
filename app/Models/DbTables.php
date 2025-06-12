<?php 
namespace App\Models;

class DbTables {

    public static $categoriesTable = 'categories';
    public static $authTokenTable = 'user_token_auth';
    public static $userTable = 'users';
    public static $accessTable = 'access';
    public static $labelsTable = 'labels';

    public static $coursesTable = 'courses';
    public static $coursesInstructorsTable = 'courses_instructors';
    public static $coursesTagsTable = 'courses_tags';
    public static $coursesReviewsTable = 'courses_reviews';
    public static $wishlistTable = 'wishlist';
    public static $contentTable = 'courses_content';

    public static $supportTable = 'support';
    public static $supportCategoryTable = 'support_categories';
    public static $supportContactTable = 'support_contacts';
    public static $supportContactRepliesTable = 'support_contacts_replies';
    public static $testimonialsTable = 'testimonials';

    public static $classesTable = 'classes';
    public static $classAttendeesTable = 'class_attendees';
    public static $assignmentsTable = 'assignments';
    public static $assignmentSubmissionsTable = 'assignment_submissions';
    public static $assignmentSubmissionFilesTable = 'assignment_submission_files';
    public static $resourcesTable = 'resources';

    public static $ticketsTable = 'tickets';
    public static $webhookTable = 'webhooks';

    public static $notificationsTable = 'notifications';
    public static $activitiesTable = 'activities';

    public static $discussionsTable = 'lesson_discussions';
    public static $notesTable = 'lesson_notes';

    public static $altUserTable = 'temp_table';
    public static $instructorsTable = 'courses_instructors';
    public static $reviewsTable = 'courses_reviews';
    public static $enrollmentsTable = 'courses_enrollments';
    public static $tagsTable = 'courses_tags';

    public static $couponTable = 'user_coupons';
    public static $promoCodeTable = 'user_promo_codes';
    
    public static $dashboardTable = 'dashboard_data';
    public static $userDashboardTable = 'user_dashboard_data';
    public static $subscriptionTable = 'user_subscriptions';
    
    public static $educationTable = 'education_center';
    public static $settingsTable = 'app_settings';

    public static $ipBlockingTable = 'ip_blocking';
    public static $paymentsTable = 'user_subscriptions_methods';
    public static $paymentsTokenTable = 'user_subscriptions_methods_token';

    public static $shareableLinksTable = 'shareable_links';
    
    public static $feedbackTable = 'user_feedbacks';
    public static $invoiceTable = 'user_subscriptions_invoices';

    /**
     * Initialize the tables
     * 
     * @return array
     */
    public static function initTables() {
        return [
            'categoriesTable', 'authTokenTable', 'userTable', 'accessTable', 'labelsTable', 'contentTable',
            'coursesTable', 'coursesInstructorsTable', 'coursesTagsTable', 'coursesReviewsTable', 'wishlistTable',
            'coursesEnrollmentsTable', 'altUserTable', 'couponTable', 'promoCodeTable', 'dashboardTable', 
            'userDashboardTable', 'subscriptionTable', 'educationTable', 'settingsTable', 'ipBlockingTable', 
            'paymentsTable', 'paymentsTokenTable', 'shareableLinksTable', 'feedbackTable', 'invoiceTable',
            'ticketsTable', 'notificationsTable', 'activitiesTable', 'discussionsTable', 'notesTable',
            'resourcesTable', 'assignmentSubmissionsTable', 'assignmentSubmissionFilesTable', 'webhookTable',
            'supportTable', 'supportCategoryTable', 'supportContactTable', 'supportContactRepliesTable',
            'testimonialsTable'
        ];
    }
}
