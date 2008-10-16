<?php

/**
 * Test reviews service.
 *
 * @package org.zenmagick.plugins.zm_tests.tests
 * @author DerManoMann
 * @version $Id$
 */
class TestZMReviews extends ZMTestCase {

    /**
     * Validate the given review as the (single) demo review.
     */
    protected function assertReview($review) {
        $this->assertTrue($review instanceof ZMReview);

        $this->assertEqual(1, $review->getId());
        $this->assertEqual(5, $review->getRating());
        $this->assertEqual(19, $review->getProductId());
        $this->assertEqual('There\'s Something About Mary Linked', $review->getProductName());
        $this->assertEqual('dvd/theres_something_about_mary.gif', $review->getProductImage());
        $this->assertEqual('This really is a very funny but old movie!', $review->getText());
        $this->assertEqual('2003-12-23 03:18:19', $review->getDateAdded());
        $this->assertEqual('Bill Smith', $review->getAuthor());
    }

    /**
     * Test load single review.
     */
    public function testReviewCount() {
        $this->assertEqual(1, ZMReviews::instance()->getReviewCount(19));
        $this->assertEqual(0, ZMReviews::instance()->getReviewCount(2));
    }

    /**
     * Test get random reviews.
     */
    public function testRandom() {
        $reviews = ZMReviews::instance()->getRandomReviews();
        $this->assertTrue(is_array($reviews));
        if ($this->assertEqual(1, count($reviews))) {
            $this->assertReview($reviews[0]);
        }
    }

    /**
     * Test get average rating.
     */
    public function testAverageRating() {
        $rating = ZMReviews::instance()->getAverageRatingForProductId(19);
        $this->assertEqual(5.0, $rating);
    }

    /**
     * Test get reviews for product.
     */
    public function testReviewsForProduct() {
        $reviews = ZMReviews::instance()->getReviewsForProductId(19);
        $this->assertTrue(is_array($reviews));
        if ($this->assertEqual(1, count($reviews))) {
            $this->assertReview($reviews[0]);
        }
    }

    /**
     * Test get all reviews.
     */
    public function testGetAll() {
        $reviews = ZMReviews::instance()->getAllReviews();
        $this->assertTrue(is_array($reviews));
        if ($this->assertEqual(1, count($reviews))) {
            $this->assertReview($reviews[0]);
        }
    }

    /**
     * Test get review for id.
     */
    public function testGetReview() {
        $review = ZMReviews::instance()->getReviewForId(1);
        $this->assertNotNull($review);
        $this->assertReview($review);
    }

    /**
     * Test update view count.
     */
    public function testUpdateViewCount() {
        $review = ZMReviews::instance()->getReviewForId(1);
        $this->assertNotNull($review);
        ZMReviews::instance()->updateViewCount(1);
        $updated = ZMReviews::instance()->getReviewForId(1);
        $this->assertNotNull($updated);
        $this->assertEqual(($review->getViewCount() + 1), $updated->getViewCount());
    }

    /**
     * Test create review.
     */
    public function testCreateReview() {
        $account = ZMAccounts::instance()->getAccountForId(1);
        if ($this->assertNotNull($account)) {
            $review = ZMLoader::make('Review');
            $review->setProductId(3);
            $review->setRating(4);
            $review->setLanguageId(1);
            $review->setText('some foo');
            $newReview = ZMReviews::instance()->createReview($review, $account);
            $this->assertTrue(0 != $newReview->getId());

            // cleanup
            $sql = 'DELETE FROM '.TABLE_REVIEWS.' WHERE reviews_id = :reviewId';
            ZMRuntime::getDatabase()->update($sql, array('reviewId' => $newReview->getId()), TABLE_REVIEWS);
            $sql = 'DELETE FROM '.TABLE_REVIEWS_DESCRIPTION.' WHERE reviews_id = :reviewId';
            ZMRuntime::getDatabase()->update($sql, array('reviewId' => $newReview->getId()), TABLE_REVIEWS_DESCRIPTION);
        }
    }

}

?>