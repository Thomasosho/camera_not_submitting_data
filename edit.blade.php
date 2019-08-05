@extends('layouts.sss')

@section('content')
    <style>
        #my_camera{
            width: 320px;
            height: 240px;
            border: 1px solid black;
        }
    </style>
    <style>
    body {font-family: Arial, Helvetica, sans-serif;}

    /* The Modal (background) */
    .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
    }

    /* Add Animation */
    @-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
    }

    @keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
    }

    /* The Close Button */
    .close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
    }

    .close:hover,
    .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
    }

    .modal-header {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
    }

    .modal-body {padding: 2px 16px;}

    .modal-footer {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
    }

    /*for the click button */

    

    </style>
    <h1>Edit Visitor</h1>

    {!! Form::open(['action' => ['PostsController@update', $post->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
    <div class="form-row align-items-center">
        <div class="col-sm-3 my-1">
            {{Form::label('visitor_name', 'Visitor Name')}}
            {{Form::text('visitor_name', $post->visitor_name, ['class' => 'form-control', 'readonly' => 'true',  'placeholder' => 'Visitor Name'])}}
        </div>
        <div class="col-sm-3 my-1">
            {{Form::label('visitor_phone', 'Visitor Phone')}}
            {{Form::text('visitor_phone', $post->visitor_phone, ['class' => 'form-control', 'readonly' => 'true', 'placeholder' => 'Visitor Phone'])}}
        </div>
        <div class="col-sm-3 my-1">
            {{Form::label('date', 'Date')}}
            {{Form::text('date', $post->date, ['class' => 'form-control', 'readonly' => 'true', 'placeholder' => 'Date'])}}
        </div>

        <div class="col-sm-3 my-1">
            {{Form::label('time_in', 'Time In')}}
            <input type="text" class="form-control" name="time-in" value="{{$post->time_in}}" readonly/>
            <button id='check-in' class="form-control" class='btn-success' type='button'
                data-href="{{url('/checkin')}}?id={{$post->id}}" <?php if ($post->time_in){ ?> disabled <?php   } ?>>Time In</button>
        </div>

        <div class="col-sm-3 my-1">
            {{Form::label('coy_name', 'COY Name')}}
            {{Form::text('coy_name', $post->coy_name, ['class' => 'form-control', 'placeholder' => 'COY Name',$post->coy_name ? 'readonly':''])}}
        </div>

        <div class="col-sm-3 my-1">
            {{Form::label('coy_address', 'COY Address')}}
            {{Form::text('coy_address', $post->coy_address, ['class' => 'form-control', 'placeholder' => 'COY Address',$post->coy_address ? 'readonly':''])}}
        </div>

        <div class="col-sm-3 my-1">
        
            {{Form::label('time_out', 'Time Out')}}
            <input type="text" class="form-control" name="time-out" value="{{$post->time_out}}" readonly/>
            
            <button id='check-out' class="form-control" class='btn-success' type='button'
                data-href="{{url('/checkout')}}?id={{$post->id}}" <?php if ($post->time_out){ ?> disabled <?php   } ?>>Time Out</button>
        </div>

        <!-- <input type="time" id="appt" class = 'form-control' name="time_in"
            min="9:00" max="18:00" placeholder='{{($post->time_out)}}' required> -->
        
        <div class="col-sm-3 my-1">
        <br>

        <!-- The Modal -->
        <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
            <h2>Capture Image</h2>
            <span class="close">&times;</span>
            </div>
            <div class="modal-body">
            <div style="width: 100%; overflow: hidden;">
                <div style="width: 600px; float: left;" id="my_camera"></div>
                <div style="margin-left: 620px;" id="results"></div>
            </div>
            <br>
            <input type="button" class="form-control" value="Take Snapshot" onClick="take_snapshot()">
            <p>{{Form::hidden('_method','PUT')}}        
            {{Form::submit('Submit', ['class'=>'btn btn-primary form-control'])}}
            </p>
            </div>
        </div>

        </div>
        <div>
            <input  type='hidden' name='sc_capture' id='sc_capture'>
            {{Form::label('cover_image', 'Cover Image')}}
            <input type="text" class="form-control" name="Cover Image" value="{{$post->cover_image}}" readonly/>
            <input type="button" id="myBtn" class="form-control" value="Take Snapshot" <?php if ($post->cover_image){ ?> disabled <?php   } ?>>

        </div>
        
        <!-- Webcam.min.js -->
        <script type="text/javascript" src="{{asset('assets/webcamjs/webcam.min.js')}}"></script>

        <!-- Configure a few settings and attach camera -->
        <script language="JavaScript">
            Webcam.set({
                width: 420,
                height: 340,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach( '#my_camera' );
        </script>
        <!-- A button for taking snaps -->
        <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("myBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
        modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
        modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        }
        </script>
        <!-- Code to handle taking the snapshot and displaying it locally -->
        <script language="JavaScript">

            function take_snapshot() {
                
                // take snapshot and get image data
                Webcam.snap( function(data_uri) {
                    // display results in page
                    document.getElementById('results').innerHTML = 
                        '<img src="'+data_uri+'"/>';
                        document.getElementById('sc_capture').value=data_uri;
                } );
            }
        </script>
            <br><br>
        </div>

        <div class="col-sm-3 my-1">
        <input type="text" class="form-control" name="status" value="{{$post->status}}" readonly/>
        </div>
        <div class="col-sm-3 my-1">
        {{Form::hidden('_method','PUT')}}        
        {{Form::submit('Submit', ['class'=>'btn btn-primary',$post->time_out ? 'disabled':''])}}
        </div>
        
    </div>
    {!! Form::close() !!}

        <script type='text/javascript' src="{{asset('assets/jquery-3.1.1.min.js')}}"></script>
    <script>
    $(function(){
        $('button#check-in').click(function(){
            location.href = '/security/posts/{{$post->id}}/edit';
            let me  = $(this);
            let url = me.data('href');
            me.attr('disabled', 'disabled').html('Timing In');

            $.get(url, {}, 'json').done(function(response){
            
                if (response.status === undefined){
                    me.removeAttr('disabled').html('Timing In');
                    alert('Error occurred, check your connection and try again.');
                    return;
                } else if (response.status !== 'success'){
                    me.removeAttr('disabled').html('Time In');
                    alert(response.message);
                    return;   
                }
                //at this point, its successful, you can decide what to do with the button
                me.html('Timed-In');
            }).fail(function(error){
                me.removeAttr('disabled').html('Timed In');
                alert('Network error occurred, check your connection and try again.');
                return; 
            });

        });

    });
    </script>

    <!-- check out-->
    <script>
    $(function(){
        $('button#check-out').click(function(){
            location.href = '/security/posts/{{$post->id}}';
            let me  = $(this);
            let url = me.data('href');
            me.attr('disabled', 'disabled').html('Timed Out');

            $.get(url, {}, 'json').done(function(response){
            
                if (response.status === undefined){
                    me.removeAttr('disabled').html('Timed Out');
                    alert('Error occurred, check your connection and try again.');
                    return;
                } else if (response.status !== 'success'){
                    me.removeAttr('disabled').html('Time Out');
                    alert(response.message);
                    return;   
                }
                //at this point, its successful, you can decide what to do with the button
                me.html('Timed-Out');
            }).fail(function(error){
                me.removeAttr('disabled').html('Time Out');
                alert('Network error occurred, check your connection and try again.');
            });

        });

    });
    </script>
    
@endsection
