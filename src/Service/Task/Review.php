<?php

namespace App\Service\Task;

use App\Service\Task;

class Review extends Task implements TaskInterface
{
    const SUBDIRECTORY_NAME = 'Review';

    /**
     * @var null|string
     */
    protected ?string $reviewDate;

    /**
     * @var null|int
     */
    protected ?int $weekOfYearReviewDate;

    /**
     * @var null|string
     */
    protected ?string $recommendationsForFurtherService;

    /**
     * @param array $item
     */
    public function __construct(array $item)
    {
        $this->number = $item['number'];
        $this->description = $item['description'];
        $this->type = self::REVIEW_TYPE_NAME;
        $this->reviewDate = null;
        $this->weekOfYearReviewDate = null;
        $this->status = null;
        $this->recommendationsForFurtherService = null;
        $this->phone = $item['phone'];
        $this->createdAt = date('Y-m-d H:i:s');

        $this->processDueDate($item['dueDate']);
    }

    /**
     * @return string|null
     */
    public function getReviewDate(): ?string
    {
        return $this->reviewDate;
    }

    /**
     * @param string|null $reviewDate
     */
    public function setReviewDate(?string $reviewDate): void
    {
        $this->reviewDate = $reviewDate;
    }

    /**
     * @return int|null
     */
    public function getWeekOfYearReviewDate(): ?int
    {
        return $this->weekOfYearReviewDate;
    }

    /**
     * @param int|null $weekOfYearReviewDate
     */
    public function setWeekOfYearReviewDate(?int $weekOfYearReviewDate): void
    {
        $this->weekOfYearReviewDate = $weekOfYearReviewDate;
    }

    /**
     * @return string|null
     */
    public function getRecommendationsForFurtherService(): ?string
    {
        return $this->recommendationsForFurtherService;
    }

    /**
     * @param string|null $recommendationsForFurtherService
     */
    public function setRecommendationsForFurtherService(?string $recommendationsForFurtherService): void
    {
        $this->recommendationsForFurtherService = $recommendationsForFurtherService;
    }

    /**
     * @param string|null $dueDate
     * @return void
     */
    protected function processDueDate(?string $dueDate): void
    {
        if (isset($dueDate) && !empty($dueDate)) {
            $time = strtotime($dueDate);

            $this->status = 'scheduled';
            $this->reviewDate = date('Y-m-d', $time);
            $this->weekOfYearReviewDate = (int)date('W', $time);
        } else {
            $this->status = 'new';
        }
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
            'reviewDate' => $this->getReviewDate(),
            'weekOfYearReviewDate' => $this->getWeekOfYearReviewDate(),
            'status' => $this->getStatus(),
            'recommendationsForFurtherService' => $this->getRecommendationsForFurtherService(),
            'phone' => $this->getPhone(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }
}