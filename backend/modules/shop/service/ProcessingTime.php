<?php

namespace backend\modules\shop\service;

use DateInterval;
use DateTime;
use Exception;

class ProcessingTime
{
    const OPTIONS_PARAMS_SWT = 'startWorkingTime';
    const OPTIONS_PARAMS_EWT = 'endWorkingTime';
    const OPTIONS_PARAMS_WEEKEND = 'weekends';

    const INTERVAL_WORKING = 'workingTime';
    const INTERVAL_NOT_WORKING = 'notWorkingTime';

    const START_WORKING_TIME_DEFAULT = '09:00:00';
    const END_WORKING_TIME_DEFAULT = '21:00:00';
    const WEEKEND_DEFAULT = [6, 7]; // Вихідні поки не враховуються при обрахунку

    const DEFAULT_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_TIME_FORMAT = 'H:i:s';
    const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /** @var string $startWorkingTime */
    protected $startWorkingTime;

    /** @var string $endWorkingTime */
    protected $endWorkingTime;

    /** @var array $weekends */
    protected $weekends;

    /** @var string $processingDateStart */
    protected $processingDateStart;

    /** @var string $processingDateEnd */
    protected $processingDateEnd;

    /** @var int $stepWorkingTime */
    public $workingTimeInterval;

    /** @var int $stepNotWorkingTime */
    public $notWorkingTimeInterval;

    /**
     * @param string $dateStart
     * @param string $dateEnd
     *
     * $options = [ 'startWorkingTime' => '09:00:00', 'endWorkingTime' => '21:00:00', 'weekends' => [6, 7] ]
     * startWorkingTime && endWorkingTime format : H:i:s
     * @param array|null $options
     * @throws Exception
     */
    public function __construct(string $dateStart, string $dateEnd, ?array $options = null)
    {
        $this->init($dateStart, $dateEnd, $options);
    }

    /**
     * @param string $dateStart
     * @param string $dateEnd
     * @param array|null $options
     * @throws Exception
     */
    public function init(string $dateStart, string $dateEnd, ?array $options)
    {
        if($dateEnd < $dateStart) {
            throw new Exception(sprintf('Invalid date range %s - %s', $dateStart, $dateEnd));
        }

        $this->initWorkingTime($options);
        $this->initProcessingTime($dateStart, $dateEnd);
        $this->calculateProcessingTime();
    }

    /**
     * @param string $dateStart
     * @param string $dateEnd
     */
    private function initProcessingTime(string $dateStart, string $dateEnd)
    {
        $this->processingDateStart = $dateStart;
        $this->processingDateEnd = $dateEnd;
    }

    /**
     * @param array|null $options
     */
    private function initWorkingTime(?array $options): void
    {
        $this->startWorkingTime = $options[self::OPTIONS_PARAMS_SWT] ?? self::START_WORKING_TIME_DEFAULT;
        $this->endWorkingTime = $options[self::OPTIONS_PARAMS_EWT] ?? self::END_WORKING_TIME_DEFAULT;
        $this->weekends = $options[self::OPTIONS_PARAMS_WEEKEND] ?? self::WEEKEND_DEFAULT;

    }

    /**
     * @throws Exception
     */
    private function calculateProcessingTime(): void
    {
        $intervalStart = [
            self::INTERVAL_WORKING => 0,
            self::INTERVAL_NOT_WORKING => 0
        ];
        $intervalEnd = [
            self::INTERVAL_WORKING => 0,
            self::INTERVAL_NOT_WORKING => 0
        ];

        $startDateTime = $this->startDateToSWT($intervalStart);
        $endDateTime = $this->endDateToSWT($intervalEnd);

        $startTime = new DateTime(self::START_WORKING_TIME_DEFAULT);
        $endTime = new DateTime(self::END_WORKING_TIME_DEFAULT);

        $workingTimeByDay = $this->getMinutesByDateInterval($startTime->diff($endTime));
        $notWorkingTimeByDay = $this->getMinutesByDateInterval($startTime->diff($endTime), true);

        $countProcessingDays = ($startDateTime->diff($endDateTime))->days;

        $this->workingTimeInterval = $intervalStart[self::INTERVAL_WORKING] + $intervalEnd[self::INTERVAL_WORKING] + ($countProcessingDays * $workingTimeByDay);
        $this->notWorkingTimeInterval = $intervalStart[self::INTERVAL_NOT_WORKING] + $intervalEnd[self::INTERVAL_NOT_WORKING] + ($countProcessingDays * $notWorkingTimeByDay);

    }

    /**
     * @param array $interval
     * @return DateTime
     * @throws Exception
     */
    private function startDateToSWT(array &$interval): DateTime
    {
        $startDateTime = new DateTime($this->processingDateStart);
        $startDate = $startDateTime->format(self::DEFAULT_DATE_FORMAT);

        $endWorkingDateTime = new DateTime($startDate . " " . $this->endWorkingTime);
        $startWorkingDateTime = new DateTime($startDate . " " . $this->startWorkingTime);

        $diff_SDT_and_EWT = $startDateTime->diff($endWorkingDateTime);

        /* $startDateTime > $endWorkingDateTime (Після закінчення робочого дня) */
        if ($diff_SDT_and_EWT->invert === 1) {
            $interval[self::INTERVAL_NOT_WORKING] -= $this->getMinutesByDateInterval($diff_SDT_and_EWT);
            $interval[self::INTERVAL_WORKING] -= $this->getMinutesByDateInterval($endWorkingDateTime->diff($startWorkingDateTime));
            return $startWorkingDateTime;
        }

        /* $startDateTime < $startWorkingDateTime (До початку робочого дня) */
        $diff_SDT_and_SWT = $startDateTime->diff($startWorkingDateTime);
        if ($diff_SDT_and_SWT->invert === 0) {
            $interval[self::INTERVAL_NOT_WORKING] = $this->getMinutesByDateInterval($diff_SDT_and_SWT);
        }

        /* $startDateTime > $startWorkingDateTime (Після початку робочого дня) */
        if ($diff_SDT_and_SWT->invert === 1) {
            $interval[self::INTERVAL_WORKING] -= $this->getMinutesByDateInterval($diff_SDT_and_SWT);
        }

        return $startWorkingDateTime;

    }

    /**
     * @param array $interval
     * @return DateTime
     * @throws Exception
     */
    private function endDateToSWT(array &$interval): DateTime
    {
        $endDateTime = new DateTime($this->processingDateEnd);
        $endDate = $endDateTime->format(self::DEFAULT_DATE_FORMAT);

        $endWorkingDateTime = new DateTime($endDate . " " . $this->endWorkingTime);
        $startWorkingDateTime = new DateTime($endDate . " " . $this->startWorkingTime);

        $diff_EDT_and_EWT = $endDateTime->diff($endWorkingDateTime);

        /* $endDateTime > $endWorkingDateTime (Після закінчення робочого дня) */
        if ($diff_EDT_and_EWT->invert === 1) {
            $interval[self::INTERVAL_NOT_WORKING] = $this->getMinutesByDateInterval($diff_EDT_and_EWT);
            $interval[self::INTERVAL_WORKING] = $this->getMinutesByDateInterval($endWorkingDateTime->diff($startWorkingDateTime));
            return $startWorkingDateTime;
        }

        /* $endDateTime < $startWorkingDateTime (До початку робочого дня) */
        $diff_EDT_and_SWT = $endDateTime->diff($startWorkingDateTime);
        if ($diff_EDT_and_SWT->invert === 0) {
            $interval[self::INTERVAL_NOT_WORKING] -= $this->getMinutesByDateInterval($diff_EDT_and_SWT);
        }

        /* $endDateTime > $startWorkingDateTime (Після початку робочого дня) */
        if ($diff_EDT_and_SWT->invert === 1) {
            $interval[self::INTERVAL_WORKING] = $this->getMinutesByDateInterval($diff_EDT_and_SWT);
        }

        return $startWorkingDateTime;
    }

    /**
     * @param DateInterval $interval
     * @param bool $invert
     * @return int
     */
    private function getMinutesByDateInterval(DateInterval $interval, bool $invert = false): int
    {
        $dateTime = 24 * 60;
        $time = ($interval->h * 60) + $interval->i;
        if ($invert) {
            return $dateTime - $time;
        }
        return $time;
    }

}