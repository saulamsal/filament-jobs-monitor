<?php

namespace Croustibat\FilamentJobsMonitor\Traits;

use Croustibat\FilamentJobsMonitor\Models\QueueMonitor;

trait QueueMonitorUpdates
{
    /**
     * Update progress.
     */
    public function setProgress(int $progress): void
    {
        $progress = min(100, max(0, $progress));

        if (!$monitor = $this->getQueueMonitor()) {
            return;
        }

        $monitor->update([
            'progress' => $progress,
        ]);

        $this->progressLastUpdated = time();
    }

    /**
     * Update custom fields.
     *
     * @param string|array $key Field key or an array of key-value pairs
     * @param mixed $value Value for the field (if $key is a string)
     */
    public function setCustomFields($key, $value = null): void
    {
        if (!$monitor = $this->getQueueMonitor()) {
            return;
        }

        // Retrieve the current custom fields and decode them into an array.
        $currentFields = json_decode($monitor->custom_fields, true) ?? [];

        // Prepare the fields to update.
        $fieldsToUpdate = is_array($key) ? $key : [$key => $value];

        // Merge the new fields with the existing ones.
        $updatedFields = array_merge($currentFields, $fieldsToUpdate);

        // Update the monitor with the merged fields, encoding them back into JSON.
        $monitor->update([
            'custom_fields' => json_encode($updatedFields),
        ]);
    }


    /**
     * Return Queue Monitor Model.
     */
    protected function getQueueMonitor(): ?QueueMonitor
    {
        if (!property_exists($this, 'job')) {
            return null;
        }

        if (!$this->job) {
            return null;
        }

        if (!$jobId = QueueMonitor::getJobId($this->job)) {
            return null;
        }

        $model = QueueMonitor::getModel();

        return $model::whereJobId($jobId)
            ->orderBy('started_at', 'desc')
            ->first();
    }
}
