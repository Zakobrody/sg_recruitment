<?php

namespace App\Service;

use App\Service\Task\FailureReport;
use App\Service\Task\Review;

class Task
{
    const REVIEW_TYPE_NAME = 'review';

    const FAILURE_REPORT_TYPE_NAME = 'failure_report';

    const UNPROCESSED_SUBDIRECTORY_NAME = 'Unprocessed';

    /**
     * @var int
     */
    protected int $number;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string|null
     */
    protected ?string $status;

    /**
     * @var string|null
     */
    protected ?string $phone;

    /**
     * @var string
     */
    protected string $createdAt;

    /**
     * @var string
     */
    protected string $subdirectoryName;


    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


    /**
     * @param array $item
     * @return FailureReport|Review
     */
    public static function factory(array $item): Review|FailureReport
    {
        if (str_contains($item['description'], 'przegląd')) {
            $task = new Review($item);
        } else {
            $task = new FailureReport($item);
        }

        return $task;
    }


}