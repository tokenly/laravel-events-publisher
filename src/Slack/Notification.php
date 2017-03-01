<?php

namespace Tokenly\EventsPublisher\Slack;

use Tokenly\EventsPublisher\Events\SlackNotification;

/*
  Notification::plainText('Hello world')->broadcast();
  Notification::good()->attach('Hi there', 'all is well')->broadcast();
  Notification::good()->attach('Happy Users', 'Happy users found', ['Users' => '3 users', 'Happiness' => 'Level 11',])->broadcast();
  Notification::good()->attach('Happy Users', '', ['Users' => '3 users', 'Happiness' => 'Level 11',])->broadcast();
 */

class Notification
{

    var $plain_text;

    protected $attachment_color = 'good';
    protected $attachments      = [];

    public static function instance() {
        return new self();
    }

    public static function plainText($text) {
        return self::instance()->setPlainText($text);
    }

    public static function good() {
        return self::instance()->setAttachmentColor('good');
    }

    public static function warning() {
        return self::instance()->setAttachmentColor('warning');
    }

    public static function danger() {
        return self::instance()->setAttachmentColor('danger');
    }

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function setPlainText($text) {
        $this->plain_text = $text;

        return $this;
    }

    public function setAttachmentColor($color) {
        $this->attachment_color = $color;
        return $this;
    }

    public function attach($title, $text, $fields=[], $params=[]) {
        $attachment = array_merge([
            'title'    => $title,
            'text'     => $text,
            'fallback' => $text,
            'color'    => $this->attachment_color,
            'fields'   => $this->normalizeFields($fields),
        ], $params);
        $this->attachments[] = $attachment;

        return $this;
    }

    public function broadcast() {
        event(new SlackNotification($this->buildNotificationData()));
        return $this;
    }

    // ------------------------------------------------------------------------

    protected function buildNotificationData() {
        $notification_data = [];
        if ($this->plain_text !== null) {
            $notification_data['text'] = $this->plain_text;
        }

        if ($this->attachments) {
            $notification_data['attachments'] = json_encode($this->attachments);
        }

        return $notification_data;

        // $fallback_text = '';
        // if (is_array($text_or_fields)) {
        //     $fields = $text_or_fields;
        //     $fallback_text = $fields[0]['value'];
        // } else {
        //     $fields = [['title' => 'Description', 'value' => $text_or_fields]];
        //     $fallback_text = $text_or_fields;
        // }
        // $fields = $this->normalizeFields($fields);

        // if (is_array($data_or_title)) {
        //     $notification_data = $data_or_title;
        // } else {
        //     $notification_data = ['title' => $data_or_title];
        // }

        // $notification_data['fields'] = $fields;
        // $notification_data['fallback'] = $fallback_text;

        // if (!isset($notification_data['color'])) { $notification_data['color'] = 'good'; }
        // return $notification_data;
    }

    protected function normalizeFields($fields_in) {
        if (array_key_exists(0, $fields_in)) {
            $unnormalized_fields = $fields_in;
        } else {
            $unnormalized_fields = [];
            foreach($fields_in as $title => $value) {
                $unnormalized_fields[] = [
                    'title' => $title,
                    'value' => $value,
                ];
            }
        }

        $fields_out = [];
        foreach($unnormalized_fields as $field) {
            if (!isset($field['short'])) {
                $field['short'] = (strlen($field['value']) < 25);
            }
            $fields_out[] = $field;
        }

        return $fields_out;
    }


}
