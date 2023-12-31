<?php

namespace Croustibat\FilamentJobsMonitor\Traits;

use Croustibat\FilamentJobsMonitor\Models\QueueMonitor;

trait QueueCustomFields
{
    /**
     * Update custom fields.
     *
     * @param string|array $key Field key or an array of key-value pairs
     * @param mixed $value Value for the field (if $key is a string)
     */
    public function updateCustomFields($key, $value = null): void
    {
        if (!$monitor = $this->getQueueMonitor()) {
            return;
        }

        $fieldsToUpdate = is_array($key) ? $key : [$key => $value];
        $currentFields = $monitor->custom_fields ?? [];
        $updatedFields = array_merge($currentFields, $fieldsToUpdate);

        $monitor->update([
            'custom_fields' => $updatedFields,
        ]);
    }

    // Reuse the getQueueMonitor method from QueueProgress trait
}
