<?php

namespace App\Service\Task;

use App\Service\Task;

class FailureReport extends Task implements TaskInterface
{
    const SUBDIRECTORY_NAME = 'FailureReport';

    /**
     * @var string|null
     */
    protected ?string $priority;

    /**
     * @var string|null
     */
    protected ?string $serviceVisitDate;

    /**
     * @var null|string
     */
    protected ?string $serviceNotes;

    public function __construct($item)
    {
        $this->number = $item['number'];
        $this->description = $item['description'];
        $this->type = self::FAILURE_REPORT_TYPE_NAME;
        $this->serviceVisitDate = $this->calculateDueDate($item['dueDate']);
        $this->serviceNotes = null;
        $this->phone = $item['phone'];
        $this->createdAt = date('Y-m-d H:i:s');
        $this->priority = null;
        $this->status = null;

        $this->setPriorityByDescription($item['description']);
        $this->setStatusByDueDate($item['dueDate']);
    }

    /**
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string|null $priority
     */
    public function setPriority(?string $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return string|null
     */
    public function getServiceVisitDate(): ?string
    {
        return $this->serviceVisitDate;
    }

    /**
     * @param string|null $serviceVisitDate
     */
    public function setServiceVisitDate(?string $serviceVisitDate): void
    {
        $this->serviceVisitDate = $serviceVisitDate;
    }

    /**
     * @return string|null
     */
    public function getServiceNotes(): ?string
    {
        return $this->serviceNotes;
    }

    /**
     * @param string|null $serviceNotes
     */
    public function setServiceNotes(?string $serviceNotes): void
    {
        $this->serviceNotes = $serviceNotes;
    }


    /**
     * @param string $description
     * @return void
     */
    protected function setPriorityByDescription(string $description): void
    {
        if (str_contains($description, 'bardzo pilne')) {
            $this->priority = 'critical';
        } elseif (str_contains($description, 'pilne')) {
            $this->priority = 'high';
        } else {
            $this->priority = 'normal';
        }
    }

    /**
     * @param string|null $dueDate
     * @return void
     */
    protected function setStatusByDueDate(?string $dueDate): void
    {
        if (isset($dueDate) && !empty($dueDate)) {
            $this->status = 'due_date';
        } else {
            $this->status = 'new';
        }
    }

    /**
     * @param string|null $date
     * @return string
     */
    protected function calculateDueDate(?string $date): string
    {
        return date('Y-m-d', strtotime($date));
    }

    public static function getSubdirectoryName(): string
    {
        return self::SUBDIRECTORY_NAME;
    }

    /**
     * @return array
     */
    public function getArrayedData(): array
    {
        return [
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'priority' => $this->getPriority(),
            'serviceVisitDate' => $this->getServiceVisitDate(),
            'status' => $this->getStatus(),
            'serviceNotes' => $this->getServiceNotes(),
            'phone' => $this->getPhone(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }
}