<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use SMSNotification;
use Carbon\Carbon;
use Twilio\Rest\Client;
use App\Post;
use App\User;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PostsController extends Controller
{

    use Notifiable;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$posts = Post::all();
        //return Post::where('title', 'Post Two')->get();
        //$posts = DB::select('SELECT * FROM posts');
        //$posts = Post::orderBy('title','desc')->take(1)->get();
        //$posts = Post::orderBy('title','desc')->get();
        $posts = Post::where('date','desc')->paginate(5);
        $posts = DB::table('posts')->paginate(5);
        return view('posts.index')->with('posts', $posts);
    }
    
    public function archive()
    {
        //$posts = Post::all();
        //return Post::where('title', 'Post Two')->get();
        //$posts = DB::select('SELECT * FROM posts');
        //$posts = Post::orderBy('title','desc')->take(1)->get();
        //$posts = Post::orderBy('title','desc')->get();
        $posts = Post::where('date','desc')->paginate(5);
        $posts = DB::table('posts')->paginate(5);
        return view('posts.archive')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        $posts = DB::table('posts')->where('visitor_name', 'like', '%'.$search.'%')->paginate(5);
        return view('posts/index', ['posts' => $posts]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'visitor_name' => 'required',
            'visitor_phone' => 'required',
            'date' => 'required',
        ]);
        

        // Create Post
        $post = new Post;
        $post->visitor_name = $request->input('visitor_name');
        $post->visitor_phone = $request->input('visitor_phone');
        $post->date = $request->input('date');
        $post->user_id = auth()->user()->id;
        $post->save();
        $this->sendMessage('You Have been Scheduled for an appointment @Solid Minerals Development Fund. Please come with this message to show them at the gate!', $post->visitor_phone);
        return redirect('/posts')->with('success', 'Visitor Created, Visitor Alerted!, reminder set');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        // Check for correct user
        if(auth()->user()->id !==$post->user_id){
            return redirect('/')->with('error', 'Unauthorized Page');
        }
        return view('posts.edit')->with('post', $post);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'visitor_name' => 'required',
            'visitor_phone' => 'required'
        ]);
         // Handle File Upload
        // if($request->hasFile('cover_image')){
            // Get filename with the extension
          //  $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just filename
            //$filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            //$extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store
            //$fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload Image
            //$path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        //} else {
          //  $fileNameToStore = 'noimage.jpg';
        //}
        // Update Post
        $post = Post::find($id);
        $post->visitor_name = $request->input('visitor_name');
        $post->visitor_phone = $request->input('visitor_phone');
        $post->coy_name = $request->input('coy_name');
        $post->coy_address = $request->input('coy_address');
        $post->date = $request->input('date');

        $image_data_uri = $request->input('sc_capture');
 //   die("URI: $image_data_uri<br><br><img src='$image_data_uri'>");

        $public_path = public_path();
        $upload_path = "{$public_path}/uploads";
        if (!\file_exists($upload_path)){
            mkdir($upload_path);
        }
        $random_no = rand(0, 100000);
        $unique_id = \uniqid(true);
        $image_rel_path = "uploads/{$random_no}.{$unique_id}.jpg";
        $image_path = "{$public_path}/{$image_rel_path}";

        //$$upload_path."/";
        //die($public_path);
       // $upload_pat
	// requires php5
    //define('UPLOAD_DIR', 'images/');

        $location = static::base64_to_jpeg($image_data_uri, $image_path);
//    $encodedData = str_replace(' ','+',$image_data_uri);
  //$decodedData = base64_decode($encodedData);

	//$success = file_put_contents($image_path, $decodedData);
    print $location ? "Saved to Paath: $image_path" : 'Unable to save the file.';
    if (!$location){
//an error has occrred, its left to how you intend handling the error
return;
    }
    
    $image_url_to_be_saved_in_db = url("{image_rel_path}");

    
    // thats it, you have the link of the image, you have the path to the image from the public folder
    // its now left for you to decide which to store as they provide the location to the image
    // That should do it from me, you should be able to figure things from here
    //die('Test now');

      //  if($request->hasFile('cover_image')){
       // $post->cover_image = $fileNameToStore;

        // $post->sc_capture = $image_url_to_be_saved_in_db;
     //   }
        $post->cover_image = $image_rel_path;
        $post->save();
        return redirect('/')->with('success', 'Visitor Updated');
    }

    public function checkin(Request $request){
        $id  = $request->get('id');
        //handles check-in
        die('Yo  bro-'.$id);

        // at this point you do your validations, and load the Post
        $timestamp = time();

        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        // Check for correct user
        if($post->cover_image != 'noimage.jpg'){
            // Delete Image
            Storage::delete('public/cover_images/'.$post->cover_image);
        }
        
        $post->delete();
        return redirect('/')->with('success', 'Visitor Removed');
    }

    private $SMS_SENDER = "SMDF";
    private $RESPONSE_TYPE = 'json';
    private $SMS_USERNAME = 'tomasosho';
    private $SMS_PASSWORD = 'samoht';


    public function getUserNumber(Request $request)
    {
        $visitor_phone = $request->input('visitor_phone');

        $message = "You Have Been Scheduled for Meeting. Come with this message on the {{$post->date}}.";

        $this->initiateSmsActivation($visitor_phone, $message);
//        $this->initiateSmsGuzzle($visitor_phone, $message);

        return redirect()->back()->with('message', 'Message has been sent successfully');
    }


    /* public function initiateSmsActivation($visitor_phone, $message){
        $isError = 0;
        $errorMessage = true;

        //Preparing post parameters
        $postData = array(
            'username' => $this->SMS_USERNAME,
            'password' => $this->SMS_PASSWORD,
            'message' => $message,
            'sender' => $this->SMS_SENDER,
            'mobiles' => $post->visitor_phone,
            'response' => $this->RESPONSE_TYPE
        );

        $url = "http://portal.bulksmsnigeria.net/api/";

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);


        //Print error if any
        if (curl_errno($ch)) {
            $isError = true;
            $errorMessage = curl_error($ch);
        }
        curl_close($ch);


        if($isError){
            return array('error' => 1 , 'message' => $errorMessage);
        }else{
            return array('error' => 0 );
        }
    }

      /**
     * Send message to a selected users
     */
    public function sendCustomMessage(Request $request)
    {
        $validatedData = $request->validate([
            'posts' => 'required|array',
            'body' => 'required',
        ]);
        $recipients = $validatedData["posts"];
        // iterate over the array of recipients and send a twilio request for each
        foreach ($recipients as $recipient) {
            $this->sendMessage($validatedData["body"], $recipient);
        }
        return back()->with(['success' => "Messages on their way!"]);
    }
    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param Number $recipients Number of recipient
     */
    private function sendMessage($message, $recipients)
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }

    static function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }

}