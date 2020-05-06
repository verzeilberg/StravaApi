<?php

namespace StravaApi\Controller;

use StravaApi\Service\StravaService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\HelperPluginManager;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Polyline;

class IndexController extends AbstractActionController
{

    /*
     * @var HelperPluginManager
     */
    protected $vhm;

    /*
     * @var StravaService
     */
    protected $stravaService;

    /*
     * Config array
     */
    protected $config;

    public function __construct(
        HelperPluginManager $vhm,
        StravaService $stravaService,
        array $config

    )
    {
        $this->vhm = $vhm;
        $this->stravaService = $stravaService;
        $this->config = $config;
    }

    public function indexAction()
    {
        $this->vhm->get('headScript')->appendFile('/js/strava.js');
        $this->vhm->get('headLink')->appendStylesheet('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css');
        $this->vhm->get('headLink')->appendStylesheet('/css/strava_style.css');
        $this->vhm->get('headScript')->appendFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');
        $this->vhm->get('headScript')->appendFile('/js/moment.js');


        $months = $this->stravaService->getMonths();

        $currentMonth = new \DateTime();
        $currentYear = $currentMonth->format('Y');
        $currentMonth = $currentMonth->format('m');
        $years = $this->stravaService->activityRepository->getYearsByActivities();


        return new ViewModel([
            'currentMonth' => $currentMonth,
            'months' => $months,
            'currentYear' => $currentYear,
            'years' => $years
        ]);
    }

    public function getChartDataAction()
    {

        $year = (int)$this->params()->fromPost('year', null);
        $month = (int)$this->params()->fromPost('month', null);
        $errorMessage = '';
        $success = true;


        $dates = $this->stravaService->getStartAndEndDateByMonthAndYear($year, $month);
        $activities = $this->stravaService->activityRepository->getActivityBetweenDates($dates['startDate'], $dates['endDate']);

        return new JsonModel([
            'errorMessage' => $errorMessage,
            'success' => $success,
            'activities' => $activities
        ]);
    }

    public function detailAction()
    {
        $this->vhm->get('headLink')->appendStylesheet('/css/strava_style.css');
        $this->vhm->get('headLink')->appendStylesheet('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css');
        $this->vhm->get('headScript')->appendFile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js');

        $id = (int)$this->params()->fromRoute('id', 0);
        if (empty($id)) {
            return $this->redirect()->toRoute('runningStats');
        }
        $activity = $this->stravaService->activityRepository->getActivityById($id);
        if (empty($activity)) {
            return $this->redirect()->toRoute('runningStats');
        }

        $encoded = $activity->getSummaryPolyline();
        $points = Polyline::decode($encoded);
        $points = Polyline::pair($points);

        $googleMapKey = $this->config['stravaSettings']['googleMapKey'];

        return [
            'activity' => $activity,
            'points' => $points,
            'rounds' => $activity->getRounds(),
            'googleMapKey' => $googleMapKey
        ];
    }
}
