<?php

class Visitors_Controller extends Base_Controller
{
    public function action_visitorsOnline()
    {

        $cacheFolderName = Request::server('HTTP_HOST');
        $cacheVisitorsName = $cacheFolderName . DS . 'visitors';

        $cacheFolderPath = path('storage') . 'cache' . DS . $cacheFolderName;
        $cacheVisitorsPath = path('storage') . 'cache' . DS . $cacheVisitorsName;

        $this->validatePath($cacheFolderPath);
        $this->validatePath($cacheVisitorsPath);

        $visitTime = 10;
        $visitorHash = hash('sha1', Request::server('HTTP_USER_AGENT') . Request::server('REMOTE_ADDR'));
        Cache::put($cacheVisitorsName . DS . $visitorHash, true, $visitTime);

        $onlineVisitors = Cache::get($cacheVisitorsName . DS . 'online', []);

        foreach ($onlineVisitors as $key => $hash) {
            if (!Cache::has($cacheVisitorsName . DS . $hash)) {
                unset($onlineVisitors[$key]);
            }
        }

        if (!in_array($visitorHash, $onlineVisitors)) {
            $onlineVisitors[] = $visitorHash;
        }

        Cache::put($cacheVisitorsName . DS . 'online', $onlineVisitors, $visitTime);

        $counter = count($onlineVisitors);

        return Response::json(array('visitors' => $counter));
    }


    private function validatePath($path)
    {
        if (!\File::exists($path)) {
            \File::mkdir($path);
        }
    }

}
