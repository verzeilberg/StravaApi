<?php

namespace StravaApi\Service;

use Doctrine\ORM\OptimisticLockException;
use StravaApi\Entity\Activity;
use User\View\Helper\CurrentUser;
use Zend\Paginator\Paginator;

interface StravaServiceInterface {

    /**
     * Get athlete from client based on athleteId
     * @param $client
     * @return array
     */
    public function getAthlete($client);

    /**
     * Get athlete activities from client
     * @param $client
     * @param $after select activities after a specific date
     * @param $page page you want to return
     * @param $per_page how many results per page
     * @return array
     */
    public function getAthleteActivities($client, $before = null, $after = null, $page = null, $per_page = null);

    /**
     * Get athlete stats from client based
     * @param $client
     * @return array
     */
    public function getAthleteStats($client);

    /**
     * Get specific activity based on activityId
     * @param $client
     * @param $activityId if of the activity
     * @return array
     */
    public function getActivity($client, $activityId = null);

    /**
     * Get all activities from client
     * @param $client
     * @param $after select activities after a specific date
     * @param $page page you want to return
     * @param $per_page how many results per page
     * @return array
     */
    public function getAllActivities($client, $before = null, $after = null, $page = null, $per_page = null);

    /**
     * Set data to new activity
     * @param $activityArr
     * @param $importLog
     * @param currentUser $currentUser whos is logged on
     * @return      void
     * @throws OptimisticLockException
     */
    public function newActivity($activityArr, $importLog, $currentUser = null);

    /**
     * Update data to new activity
     * @param $activityArr
     * @param $activityId
     * @param currentUser $currentUser whos is logged on
     * @return      void
     * @throws OptimisticLockException
     */
    public function updateActivity($activityArr, $activityId, $currentUser = null);

    /**
     * Set data to existing activity
     * @param activity $activity object
     * @param currentUser $currentUser who is logged on
     * @return      void
     * @throws Exception
     */
    public function setExistingActivity($activity, $currentUser);

    /**
     * Get array of activities for pagination
     * @param string $query
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Paginator
     */
    public function getItemsForPagination($query, $currentPage = 1, $itemsPerPage = 10);

    /**
     * Get start and end date based on given year and/or month
     * @return array
     * @throws Exception
     * @var $year year
     * @var $month month
     */
    public function getStartAndEndDateByMonthAndYear($year = null, $month = null);

    /**
     * Returns an array of months
     * @return array
     */
    public function getMonths();

}
