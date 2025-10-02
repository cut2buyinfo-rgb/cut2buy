<?php require('partials/header.php'); ?>

<main>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <h1 class="text-center mb-4">সাধারণ জিজ্ঞাসা (FAQ)</h1>
                <p class="text-center text-muted mb-5">
                    আপনার মনে থাকা প্রশ্নগুলোর উত্তর খুঁজে নিন।
                </p>

                <div class="accordion" id="faqAccordion">

                    <!-- প্রশ্ন ১: অর্ডার -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                আমি কিভাবে অর্ডার করব?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                অর্ডার করা খুবই সহজ! আপনার পছন্দের পণ্যটি বেছে নিয়ে 'Add to Cart' বাটনে ক্লিক করুন। এরপর কার্টে গিয়ে 'Checkout' বাটনে ক্লিক করে আপনার নাম, ঠিকানা এবং মোবাইল নম্বর দিয়ে অর্ডারটি সম্পন্ন করুন।
                            </div>
                        </div>
                    </div>

                    <!-- প্রশ্ন ২: পেমেন্ট -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                পেমেন্ট করার কি কি উপায় আছে?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                আমরা বর্তমানে 'ক্যাশ অন ডেলিভারি' (পণ্য হাতে পেয়ে টাকা পরিশোধ) পদ্ধতিতে পেমেন্ট গ্রহণ করছি। খুব শীঘ্রই আমরা অনলাইন পেমেন্ট (বিকাশ, নগদ, রকেট, কার্ড) চালু করব।
                            </div>
                        </div>
                    </div>

                    <!-- প্রশ্ন ৩: ডেলিভারি -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                ডেলিভারি পেতে কতদিন সময় লাগে?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                ঢাকার মধ্যে অর্ডার করার পর ২-৩ কার্যদিবসের মধ্যে ডেলিভারি দেওয়া হয়। ঢাকার বাইরে এটি পৌঁছাতে সাধারণত ৪-৫ কার্যদিবস সময় লাগে।
                            </div>
                        </div>
                    </div>

                    <!-- প্রশ্ন ৪: রিটার্ন পলিসি -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                পণ্য ফেরত দেওয়ার নিয়ম কি?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                আপনি যদি কোনো ত্রুটিপূর্ণ বা ভুল পণ্য পেয়ে থাকেন, তবে ডেলিভারি নেওয়ার ২৪ ঘণ্টার মধ্যে আমাদের সাথে যোগাযোগ করুন। আমাদের টিম আপনাকে পণ্যটি পরিবর্তন বা ফেরত দেওয়ার জন্য সহায়তা করবে। অনুগ্রহ করে মনে রাখবেন, পণ্যের প্যাকেজিং অক্ষত থাকতে হবে।
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</main>

<?php require('partials/footer.php'); ?>