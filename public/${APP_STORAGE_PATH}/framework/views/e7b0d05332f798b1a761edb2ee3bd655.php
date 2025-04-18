<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><?php echo e(__('Register')); ?></div>

                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('register')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Username')); ?></label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" name="name" value="<?php echo e(old('name')); ?>" required autofocus>

                                <?php if($errors->has('name')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right"><?php echo e(__('First Name')); ?></label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control<?php echo e($errors->has('first_name') ? ' is-invalid' : ''); ?>" name="first_name" value="<?php echo e(old('first_name')); ?>" required autofocus>

                                <?php if($errors->has('first_name')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('first_name')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Last Name')); ?></label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control<?php echo e($errors->has('last_name') ? ' is-invalid' : ''); ?>" name="last_name" value="<?php echo e(old('last_name')); ?>" required autofocus>

                                <?php if($errors->has('last_name')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('last_name')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div> -->

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right"><?php echo e(__('E-Mail Address')); ?></label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required>

                                <?php if($errors->has('email')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password_hash" class="col-md-4 col-form-label text-md-right"><?php echo e(__('password')); ?></label>

                            <div class="col-md-6">
                                <input id="password_hash" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required>

                                <?php if($errors->has('password')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Confirm Password')); ?></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>


                        <?php if(config('settings.reCaptchStatus')): ?>
                            <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-4">
                                    <div class="g-recaptcha" data-sitekey="<?php echo e(config('settings.reCaptchSite')); ?>"></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group row mb-4">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block"><?php echo e(__('Register')); ?></button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                                <p class="text-center mb-4">
                                    Or Use Social Logins to Register
                                </p>
                                <?php echo $__env->make('partials.socials', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer_scripts'); ?>
    <?php if(config('settings.reCaptchStatus')): ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\DUBBATECH\Ruyas-app\resources\views/auth/register.blade.php ENDPATH**/ ?>