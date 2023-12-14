<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;
use Ixudra\Curl\Facades\Curl;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJqdGkiOiI3OTdhNjc4Zi01NWQyLTRkMDUtOThhMy0xODFhMzkyMGM0OGQiLCJ1c2VyaWQiOiIxMTc1MCIsInVzZXJuYW1lIjoiYXBpQHRoYnJlZC5jb20iLCJ1c2VyY29tcGFueWlkIjoiNzIyIiwidXNlcnBhY2thZ2VpZCI6IjEiLCJ1c2VyaWdub3JldGhyb3R0bGUiOiJGYWxzZSIsImlzY2xpZW50dXNlciI6IlRydWUiLCJpc2RhdGFhZG1pbiI6IkZhbHNlIiwiaHR0cDovL3NjaGVtYXMueG1sc29hcC5vcmcvd3MvMjAwNS8wNS9pZGVudGl0eS9jbGFpbXMvbmFtZSI6ImFwaUB0aGJyZWQuY29tIiwiZXhwIjoxNjU3MjYyNjM5fQ.oIRlj94E4DLdOuE1ODLweDPPulkMcBdvDdAWaR70CUo";


    public function sendEmail($toEmail,$toName,$subject,$body,$viewName='mail',$param=array()){

        // $fromEmail = ;
        try {
            Mail::send($viewName, $param, function ($m) use ($toEmail,$toName,$subject) {
            $m->from(env('MAIL_FROM_ADDRESS','smtp@itechnolabs.tech'), env('MAIL_FROM_NAME','Safe Exam'));

            $m->to($toEmail, $toName)->subject($subject);
        }); 
    }catch (Exception $ex) {
            \Log::info($ex->getMessage());
        }
            
    }

    // public function getLookUpApiResponse($apiUrl,$pageNo){

    //     $response = Curl::to($apiUrl.'/en/api/IntegrationApi/LookUp')
    //                     ->withHeader('Authorization: Bearer '.$this->token)
    //                     ->withData( [ 'SelectedPageIndex' =>  $pageNo ] )
    //                     ->post();

    //     return $response;
    // }

    // public function getDepositorSearchApiResponse($apiUrl,$customerId){
    //     $response = Curl::to($apiUrl.'/en/api/IntegrationApi/DepositorSearch')
    //                     ->withHeader('Authorization: Bearer '.$this->token)
    //                     ->withData( [ 'ID' =>$customerId ] )
    //                     ->post();

    //     return $response;
    // }

    // public function getPartyAddressSearchApiResponse($apiUrl,$partyId){
    //     $response = Curl::to($apiUrl.'/en/api/IntegrationApi/PartyAddressSearch')
    //                     ->withHeader('Authorization: Bearer '.$this->token)
    //                     ->withData( [ 'PartyID' =>$partyId ] )
    //                     ->post();

    //     return $response;
    // }
    // public function getPartyAddressGetApiResponse($apiUrl,$partyAddressId){
    //     $response = Curl::to($apiUrl.'/en/api/IntegrationApi/PartyAddressGet')
    //                     ->withHeader('Authorization: Bearer '.$this->token)
    //                     ->withData( [ 'ID' =>$partyAddressId ] )
    //                     ->post();
    //     return $response;
    // }
}
