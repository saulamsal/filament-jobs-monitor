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

        $fieldsToUpdate = is_array($key) ? $key : [$key => $value];
        $currentFields = $monitor->custom_fields ?? [];
        $updatedFields = array_merge($currentFields, $fieldsToUpdate);

        $monitor->update([
            'custom_fields' => $updatedFields,
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
