<?php

namespace App\Http\Controllers;
use App\Models\File;
use App\Notifications\ShareNotification;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    public function index()
    {
        $userId= Auth::id();

        $files = DB::table('media')->whereJsonContains('custom_properties',['userID'=>$userId])->get();

         if ($files->isEmpty())
             $files=null;
         return view('disk.index',['files'=>$files,'shared_flag'=>false]);
    }

    public function store()
    {
        if(request('file') == null)
        return back();

        $userId= Auth::id();
        $file = File::create(['user'=>$userId]);

        $access_list = array();
        array_push($access_list, $userId);
        array_push($access_list);

        $properties = ['userID' => $userId, 'access' => $access_list,'hash'=>hash("haval160,4",$file->id),'link_enabled'=>false];

        $media = $file
            ->addMediaFromRequest('file')
            ->withCustomProperties($properties) //middle method
            ->preservingOriginal()
            ->toMediaCollection();

        return( redirect("/disk"));
    }
    public function upload()
    {
       return view('disk.upload');
    }

    public function download(Media $disk)
    {
        $file = File::query()->find($disk['id']);
        $access = $disk['custom_properties'];
        $access = $access['access'];
        $shared = false;
        foreach ($access as $single_id)
        {
            if ($disk['id'] == $single_id)
                $shared = true;
        }

        if($this->permission_check($file) == false & $shared = false)
        abort(404);

        return $disk;
    }

    public function link_download()
    {
        $ar = explode("/",$_SERVER['REQUEST_URI']);
        $hash = $ar[2];
        $media = DB::table('media')->whereJsonContains('custom_properties',['hash'=>$hash])->get();
        $media = Media::findByUuid($media[0]->uuid);
        if(!$media->custom_properties['link_enabled'])
            abort(404);
        return $media;
    }

    public function link(File $disk)
    {
        $hash = hash("haval160,4",$disk['id']);
        $path = $_SERVER['HTTP_HOST']."/disk/".$hash."/download_link";
        $media = DB::table('media')->where('id','=',$disk['id'])->get(); // wrzut do media info ze user o zadanym id ma dostep
        $custom_properties= $media[0]->custom_properties;
        $custom_properties = json_decode($custom_properties);
        $custom_properties->link_enabled = true;
        $custom_properties = json_encode($custom_properties);
        DB::table('media')->where('id','=',$disk['id'])->update(['custom_properties'=>$custom_properties]); // wrzut do media info ze user o zadanym id ma dostep

        return view('disk.download',['path'=>$path,'disk'=>$disk]);
    }

    public function delete(File $disk)
    {
        if($disk['user']!=Auth::id())
            abort(404);

        $disk->delete();

        return back();
    }

    public function soft_delete(File $disk)
    {
        if(!$this->permission_check($disk))
            abort(404);

        $user = DB::table('users')->where('id','=',Auth::id())->get();
        $user=$user[0];
        $access_array = (array)json_decode($user->access);

        $key = array_search($disk['id'], $access_array);

        array_splice($access_array,$key,1);
        DB::table('users')->where('id','=',Auth::id())->update(['access'=>$access_array]);

        $custom_properties =   DB::table('media')->where('id','=',$disk['id'])->get();
        $custom_properties = json_decode($custom_properties[0]->custom_properties);

        $key = array_search(Auth::id(), $custom_properties->access);
        $custom_properties = (array)$custom_properties;
        array_splice($custom_properties['access'],$key,1);
        $custom_properties = json_encode($custom_properties);

        DB::table('media')->where('id','=',$disk['id'])->update(['custom_properties'=>$custom_properties]);

        return back();
    }

    public function shared()
    {
        $userId= Auth::id();
        $files = DB::table('users')->where('id','=',$userId)->get();
        $files = $files[0];
        $files_add = json_decode($files->access);

        $files = array();
        if(!$files_add==null)
            foreach ($files_add as $fileID)
        {
            $val = DB::table('media')->where('id','=',$fileID)->get();

            if (!$val->isEmpty())
                array_push($files,$val[0]);
            else
            {
                $files_add=(array)$files_add;
                $key = array_search($fileID, $files_add);
                array_splice($files_add,$key,1);
                DB::table('users')->where('id','=',Auth::id())->update(['access'=>$files_add]);
            }
        }
        return view('disk.index',['files'=>$files,'shared_flag'=>true]);
    }

    public function share()
    {
        return view('disk.share');
    }

    public function share_my_file(File $disk)
    {
        $email = $_POST['email'] ?? "";
        $arr = explode("/", $_SERVER['REQUEST_URI']);
        $file_id = $arr[2];

        $friend = DB::table('users')->where('email','=',$email)->get();
        $file = DB::table('media')->where('id','=',$file_id)->get()->first();


        $user = User::query()->where('email','=',$email)->get();

        if($user->isEmpty()) //jezeli nie ma takiego usera
        {
            header("Location: /disk");
            return view('disk.share');
        }

        $user=$user[0];
        $user->notify(new ShareNotification($file->file_name,Auth::user()['email']));

        if(!$this->permission_check($disk))
        abort(404);

        if (!$friend->isEmpty())
        {
            $friend_access_list = json_decode($friend[0]->access);
            if ($friend_access_list==null) $friend_access_list=array();
            $friend_access_list = (array)$friend_access_list;
            array_push($friend_access_list,$disk['id']);
            $friend_access_list= array_unique($friend_access_list);

            DB::table('users')->where('email', '=', $email)->update(['access' => $friend_access_list]); //wrzut dostepnego pliku do usera
            $acces_temp = DB::table('media')->where('id','=',$disk['id'])->get();
            $custom_properties = json_decode($acces_temp[0]->custom_properties);
            array_push($custom_properties->access,$friend[0]->id);
            $custom_properties->access=array_unique($custom_properties->access);
            $custom_properties = json_encode($custom_properties);
            DB::table('media')->where('id','=',$disk['id'])->update(['custom_properties'=>$custom_properties]); // wrzut do media info ze user o zadanym id ma dostep
        }

        header("Location: /disk");

        return view('disk.share');
    }

    public function permission_check(File $disk)
    {
        $validation_shared = DB::table('media')->whereJsonContains('custom_properties',['access'=>Auth::id()])->get();

        foreach ($validation_shared as $file)
        {
            if($file->id == $disk['id'])
            return true;
        }
        return false;
    }

    public function notifications()
    {
        return view('disk.notifications', ['notifications' => auth()->user()->notifications]);
    }
}
