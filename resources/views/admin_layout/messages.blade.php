<?php
                        if ($errors->any()) {
                            foreach ($errors->all() as $error) {
                                ?>
                                <h6 class="alert alert-danger"> <?php echo $error ?></h6>
                                <?php
                            }
                        }
                        if (Session::has('success')) {
                            ?>
                            <div class="alert alert-success">
                                <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                <?php echo Session::get('success') ?>
                            </div>
                            <?php
                        }
                        if (Session::has('message')) {
                            ?>
                            <div class="alert alert-info alert-dismissible">
                                <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                <?php echo Session::get('message') ?>
                            </div>
                            <?php
                        }
                        if (Session::has('logincheck')) {
                            ?>
                            <div class="alert alert-info alert-dismissible">
                                <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                <?php echo Session::get('logincheck') ?>
                            </div>
                            <?php
                        }
                        if (Session::has('error')) {
                            ?>
                            <div class="alert alert-danger">
                                <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                <?php echo Session::get('error') ?>
                            </div>
                        <?php } ?>