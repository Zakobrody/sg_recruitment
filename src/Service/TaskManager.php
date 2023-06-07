<?php

namespace App\Service;

use App\Service\Task\FailureReport;
use App\Service\Task\Review;
use Exception;

class TaskManager
{

    /**
     * @var string
     */
    private string $taskDirectory;

    /**
     * @var Task
     */
    private Task $task;

    /**
     * @var Review[]
     */
    private array $reviewList;

    /**
     * @var FailureReport[]
     */
    private array $failureReportList;

    /**
     * @var array
     */
    private array $unprocessedList;

    public function __construct($taskDirectory, Task $task)
    {
        $this->taskDirectory = $taskDirectory;
        $this->task = $task;
    }

    /**
     * @param string $fileName
     * @return bool|string
     * @throws Exception
     */
    public function getTasksFromFile(string $fileName): bool|string
    {
        $filePath = $this->taskDirectory . DIRECTORY_SEPARATOR . $fileName;
        if (!is_readable($filePath)) {
            throw new Exception("The file: ({$fileName}) does not exist. Please choose valid file.");
        }

        return file_get_contents($filePath);
    }

    /**
     * @param string $tasksJSON
     * @return array
     */
    public function processTasks(string $tasksJSON): array
    {
        $arrayedData = $this->convertJsonToArray($tasksJSON);

        $i = 0;
        $processedList = [];
        foreach ($arrayedData as $key => $item) {
            $i++;

            if($this->checkIsDuplicated($item, $processedList)) {
                $item['error_message'] = "This item is duplicated";
                $this->unprocessedList[] = $item;
                continue;
            }
            $processedList[] = $item['description'];

            $task = $this->task::factory($item);

            switch ($task->getType()) {
                case Task::REVIEW_TYPE_NAME:
                    $this->reviewList[] = $task;
                    break;
                case Task::FAILURE_REPORT_TYPE_NAME:
                    $this->failureReportList[] = $task;
                    break;
                default:
                    $this->unprocessedList[] = $item;
            }
        }

        $numberOfReview = count($this->reviewList);
        $numberOfReportList = count($this->failureReportList);

        $fileName = date('Y_m_d__H_i_s') . ".json";

        $filePath = $this->taskDirectory . DIRECTORY_SEPARATOR . FailureReport::getSubdirectoryName();
        $this->writeDataToFile($this->convertListToJson($this->failureReportList), $filePath, $fileName);

        $filePath = $this->taskDirectory . DIRECTORY_SEPARATOR . Review::getSubdirectoryName();
        $this->writeDataToFile($this->convertListToJson($this->reviewList), $filePath, $fileName);

        $filePath = $this->taskDirectory . DIRECTORY_SEPARATOR . Task::UNPROCESSED_SUBDIRECTORY_NAME;
        $this->writeDataToFile($this->convertArrayToJson($this->unprocessedList), $filePath, $fileName);

        return [
            'numberOfReview' => $numberOfReview,
            'numberOfReportList' => $numberOfReportList,
            'allItemsCount' => $i,
            'unprocessedList' => $this->unprocessedList
        ];
    }

    /**
     * @param array $item
     * @param array $processedList
     * @return bool
     */
    protected function checkIsDuplicated(array $item, array $processedList): bool
    {
        return in_array($item['description'], $processedList);
    }

    /**
     * @param $data string
     * @param $directory string
     * @param $fileName string
     * @return bool|int
     */
    protected function writeDataToFile(string $data, string $directory, string $fileName): bool|int
    {
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        return file_put_contents($directory . DIRECTORY_SEPARATOR . $fileName, $data);
    }

    /**
     * @param Review[]|FailureReport[] $list
     * @return string
     */
    protected function convertListToJson(array $list): string
    {
        $data = [];

        foreach ($list as $item) {
            $data[] = $item->getArrayedData();
        }

        return $this->convertArrayToJson($data);
    }

    /**
     * @param array $list
     * @return string
     */
    protected function convertArrayToJson(array $list): string
    {
        return json_encode($list, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $jsonData
     * @return array
     */
    public function convertJsonToArray(string $jsonData): array
    {
        return json_decode($jsonData, true);
    }

}