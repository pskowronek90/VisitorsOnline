<?php
 
class Visitors_Controller extends BaseController
{
    public function action_visitorsOnline($page)
    {
        $visitTime = 10;
 
        $guestHash = hash('sha1', Request::server('HTTP_USER_AGENT').Request::server('REMOTE_ADDR'));
        Cache::put($userHash, true, $visitTime);
 
        $onlineGuests = Cache::get('online', []);
 
        foreach ($onlineGuests as $key => $hash)
        {
            if (!Cache::has($hash)) {
                unset($online[$key]);
            }
        }
 
        if (!in_array($guestHash, $onlineGuests)) {
            $onlineGuests[] = $guestHash;
        }
 
        Cache::put('online', $online, $visitTime);
 
        $counter = count($online);
 
        // show data
        $data = [
            'guestHash' => $guestHash,
            'onlineGuests' => $onlineGuests,
            'counter' => $counter,
        ];
 
        return View::make('visitors', $data);
    }
}
