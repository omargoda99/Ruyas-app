<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Auth | <?php echo e(trans('terms.publicPage.title')); ?></title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 64px;
            }

            .title small {
                font-size: 60px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .container {
                max-width: 600px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <?php if(Route::has('login')): ?>
                <div class="top-right links">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/home')); ?>">Home</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>">Login</a>

                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>">Register</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="content">
                <div class="title m-b-md">
                    <?php echo e(trans('terms.publicPage.title')); ?>

                </div>
                <div class="container">
                    <p>
                        <?php echo e(trans('terms.publicPage.term1')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term2')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term3')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term4')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term5')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term6')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term7')); ?>

                    </p>
                    <p>
                        <?php echo e(trans('terms.publicPage.term8')); ?>

                    </p>
                </div>

            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\DUBBATECH\Ruyas-app\resources\views/pages/public/terms.blade.php ENDPATH**/ ?>