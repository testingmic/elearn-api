<?php
/**
 * Format the contacts
 * 
 * @param array $contacts
 * @return array
 */
function formatContacts($contacts) {
    if(empty($contacts)) return [];

    foreach($contacts as $contact) {
        $result[] = [
            "id" => $contact['id'],
            "name" => $contact['name'],
            "email" => $contact['email'],
            "subject" => $contact['subject'],
            "message" => $contact['message'],
            "created_at" => $contact['created_at'],
            "updated_at" => $contact['updated_at'],
            "repliesCount" => $contact['repliesCount'],
            "category_id" => 0,
        ];
    }

    return $result ?? [];
}

/**
 * Format the consultancy requests
 * 
 * @param array $contacts
 * @return array
 */
function consultancyRequests($contacts) {

    if(empty($contacts)) return [];

    foreach($contacts as $contact) {
        $result[] = [
            "id" => $contact['id'],
            "name" => $contact['name'],
            "email" => $contact['email'],
            "phone" => $contact['phone'],
            "message" => $contact['message'],
            "created_at" => $contact['created_at'],
            "updated_at" => $contact['updated_at'],
            "repliesCount" => $contact['repliesCount'],
            "request_type" => $contact['request_type'],
            "organization" => $contact['organization'],
            "project_title" => $contact['project_title'],
            "budget" => $contact['budget'],
            "timeline" => $contact['timeline'],
            "attachments" => $contact['attachments']
        ];
    }

    return $result ?? [];

}
/**
 * Format the support replies
 * 
 * @param array $replies
 * @return array
 */
function formatSupportReplies($replies = []) {

    if(empty($replies)) return [];

    foreach($replies as $reply) {
        $reply['user'] = json_decode($reply['user'], true);
        unset($reply['created_by']);
        unset($reply['contact_id']);
        $result[] = $reply;
    }

    return $result ?? [];
}
?>