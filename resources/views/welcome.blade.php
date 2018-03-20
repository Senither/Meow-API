<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME') }} - A simple cat API</title>

    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/flatly/bootstrap.min.css"></link>
    <style type="text/css">
        h1 {
            text-align: center;
            padding-top: 45px;
            padding-bottom: 25px;
        }
        .clearfix {
            clear: both;
            padding-bottom: 20px;
        }
        a.try {
            float: right;
        }
        .resource {
            margin-right: 12px;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container"> 
            <div class="row justify-content-center"> 
                <h1>{{ env('APP_NAME') }}</h1>
            </div>

            <div class="row justify-content-center"> 
                <div class="col-md-8"> 
                    <div class="card"> 
                        <div class="card-header">
                            Information
                        </div> 
         
                        <div class="card-body"> 
                            <p>{{ env('APP_NAME') }} is a free and <a href="https://github.com/Senither/Meow-API">open-source</a> API created by <a href="https://github.com/Senither">Alexis Tan</a>, the API uses a resource-to-cost rate limit, with unauthorized requested getting 60 resources per minute, different API endpoints will cost more resources than others, you can see what an endpoint costs below. If you'd like to use the API with more resources available, shoot me a message on Discord at <a href="https://discord.gg/ZgPUtcs">https://discord.gg/ZgPUtcs</a> or at <code>Senither#0001</code> to get an API token with a raised limit.</p>

                            <p>You can see your request limit and amout of requests you have left by checking for the <code>X-RateLimit-Limit</code> and the <code>X-RateLimit-Remaining</code> headers.</p>

                            <p>Currently serving <code>{{ number_format($total) }}</code> cat pictures to the world-wide-web. Wanna add a cat picture to the API? Join the <a href="https://discord.gg/ZgPUtcs">Discord Server</a> and upload your image to the <a href="https://discord.gg/ZgPUtcs">#meow</a> channel to get it added!</p>
                        </div> 
                    </div> 
                </div> 
            </div>

            <div class="clearfix"></div>

            <div class="row justify-content-center"> 
                <div class="col-md-8"> 
                    <div class="card"> 
                        <div class="card-header">
                            Image - Random
                            <a href="{{ env('APP_SITE') }}v1/random" class="btn btn-info btn-sm try">Try it out</a>
                            <span class="btn btn-sm btn-primary resource float-right">Costs <strong>5</strong> Resources</span>
                        </div> 
         
                        <div class="card-body"> 
                            <p>Gets a random cat image from the API.</p>

                            <p><span class="btn btn-sm btn-success">GET</span> <code>/v1/random</code></p>
                            <p><strong>Example:</strong></p>
                            <p>
                                <code>{{ env('APP_SITE') }}v1/random</code>
                            </p>
                        </div> 
                    </div> 
                </div> 
            </div>

            <div class="clearfix"></div>

            <div class="row justify-content-center"> 
                <div class="col-md-8"> 
                    <div class="card"> 
                        <div class="card-header">
                            Image - Show image
                            <a href="{{ env('APP_SITE') }}v1/image/{{ $image->file }}" class="btn btn-info btn-sm try">Try it out</a>
                            <span class="btn btn-sm btn-primary resource float-right">Costs <strong>1</strong> Resource</span>
                        </div> 
         
                        <div class="card-body"> 
                            <p>Gets the image data with the given file name.</p>

                            <p><span class="btn btn-sm btn-success">GET</span> <code>/v1/image/:file</code></p>

                            <p><strong>Example:</strong></p>
                            <p>
                                <code>{{ env('APP_SITE') }}v1/image/some-file-name</code>
                            </p>
                        </div> 
                    </div> 
                </div> 
            </div>

            <div class="clearfix"></div>

            <div class="row justify-content-center"> 
                <div class="col-md-8"> 
                    <div class="card"> 
                        <div class="card-header">
                            Image - Get from type
                            <a href="{{ env('APP_SITE') }}v1/type/{{ $image->type }}" class="btn btn-info btn-sm try">Try it out</a>
                            <span class="btn btn-sm btn-primary resource float-right">Costs <strong>20</strong> Resources</span>
                        </div> 
         
                        <div class="card-body"> 
                            <p>Gets up to 10 images of the given type per page, image types are things like <code>jpg</code>, <code>png</code>, and <code>gif</code>.</p>

                            <p><span class="btn btn-sm btn-success">GET</span> <code>/v1/type/:type</code></p>

                            <p><strong>Example:</strong></p>
                            <p>
                                <code>{{ env('APP_SITE') }}v1/type/some-file-type</code>
                            </p>
                        </div> 
                    </div> 
                </div> 
            </div>
        </div> 
    </div>
</body>
</html>