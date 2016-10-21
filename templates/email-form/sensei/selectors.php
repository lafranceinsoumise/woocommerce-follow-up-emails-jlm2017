<div>
    <p class="form-field sensei sensei-courses">
        <label for="course_id"><?php _e('Enable for', 'follow_up_emails'); ?></label>
        <input
            type="hidden"
            id="course_id"
            name="meta[sensei_course_id]"
            class="sensei_courses sensei-search"
            data-placeholder="<?php _e('All courses&hellip;', 'woocommerce'); ?>"
            data-action="fue_sensei_search_courses"
            data-nonce="<?php echo wp_create_nonce("search-courses"); ?>"
            value="<?php echo $course_id; ?>"
            data-selected="<?php echo !empty($course_id) ? get_the_title( $course_id ) : ''; ?>"
            style="width: 100%"
            >
    </p>

    <p class="form-field sensei sensei-lessons">
        <label for="lesson_id"><?php _e('Enable for', 'follow_up_emails'); ?></label>
        <input
            type="hidden"
            id="lesson_id"
            name="meta[sensei_lesson_id]"
            class="sensei_lessons sensei-search"
            data-placeholder="<?php _e('All lessons&hellip;', 'woocommerce'); ?>"
            data-action="fue_sensei_search_lessons"
            data-nonce="<?php echo wp_create_nonce("search-lessons"); ?>"
            value="<?php echo $lesson_id; ?>"
            data-selected="<?php echo !empty($lesson_id) ? get_the_title( $lesson_id ) : ''; ?>"
            style="width: 100%"
            >
    </p>

    <p class="form-field sensei sensei-quizzes">
        <label for="quiz_id"><?php _e('Enable for', 'follow_up_emails'); ?></label>
        <input
            type="hidden"
            id="quiz_id"
            name="meta[sensei_quiz_id]"
            class="sensei_quizzes sensei-search"
            data-placeholder="<?php _e('All quizzes&hellip;', 'woocommerce'); ?>"
            data-action="fue_sensei_search_quizzes"
            data-nonce="<?php echo wp_create_nonce("search-quizzes"); ?>"
            value="<?php echo $quiz_id; ?>"
            data-selected="<?php echo !empty($quiz_id) ? get_the_title( $quiz_id ) : ''; ?>"
            style="width: 100%"
            >
    </p>

    <p class="form-field sensei sensei-answers">
        <label for="question_id"><?php _e('Question', 'follow_up_emails'); ?></label>
        <input
            type="hidden"
            id="question_id"
            name="meta[sensei_question_id]"
            class="sensei_questions sensei-search"
            data-action="fue_sensei_search_questions"
            data-nonce="<?php echo wp_create_nonce("search-questions"); ?>"
            data-placeholder="<?php _e('Search for a question', 'follow_up_emails'); ?>"
            value="<?php
            $question_name = '';

            if ( !empty( $question_id ) ) {
                $question_name = wp_kses_post( get_the_title( $question_id ) );
            }

            echo $question_id;
            ?>"
            data-selected="<?php echo esc_attr( $question_name ); ?>"
            style="width: 100%"
            >

        <label for="answer"><?php _e('Answer', 'follow_up_emails'); ?></label>
        <input type="text" class="input-text" name="meta[sensei_answer]" id="answer" value="<?php echo esc_attr($answer); ?>" style="width: 90%;" />
    </p>
</div>