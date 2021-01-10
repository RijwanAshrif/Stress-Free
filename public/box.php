<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Comment Box</title>
  <link rel="stylesheet" href="./style4.css">
  <link rel="shortcut icon" type="image/png" href="favicon.png">

</head>
<body>
<!-- partial:index.partial.html -->
<div class="comments-app" ng-app="commentsApp" ng-controller="CommentsController as cmntCtrl">
  <h2 align="center" >If you wants to join our survey then: <a href="https://forms.gle/mhHYiX5RG5SNtR638">click here</a></h2>
  <br>
  <h1>If you have any comments/suggestions write here:</h1>
  
  
  
  <!-- From -->
  <div class="comment-form">
    <!-- Comment Avatar -->
    <div class="comment-avatar">
      <img src="user1.png">
    </div>

    <form class="form" name="form" ng-submit="form.$valid && cmntCtrl.addComment()" novalidate>
      <div class="form-row">
        <textarea
                  class="input"
                  ng-model="cmntCtrl.comment.text"
                  placeholder="Add comment..."
                  required></textarea>
      </div>

      <div class="form-row">
        <input
               class="input"
               ng-class="{ disabled: cmntCtrl.comment.anonymous }"
               ng-disabled="cmntCtrl.comment.anonymous"
               ng-model="cmntCtrl.comment.author"
               ng-required="!cmntCtrl.comment.anonymous"
               placeholder="Email"
               type="email">
      </div>

      <div class="form-row text-right">
        <input
               id="comment-anonymous"
               ng-change="cmntCtrl.anonymousChanged()"
               ng-model="cmntCtrl.comment.anonymous"
               type="checkbox">
        <label for="comment-anonymous">Anonymous</label>
      </div>

      <div class="form-row">
        <input type="submit" value="Add Comment">
      </div>
    </form>
  </div>

  <!-- Comments List -->
  <div class="comments">
    <!-- Comment -->
    <div class="comment" ng-repeat="comment in cmntCtrl.comments | orderBy: '-date'">
      <!-- Comment Avatar -->
      <div class="comment-avatar">
        <img ng-src="user1.png">
      </div>

      <!-- Comment Box -->
      <div class="comment-box">
        <div class="comment-text">{{ comment.text }}</div>
        <div class="comment-footer">
          <div class="comment-info">
            <span class="comment-author">
              <em ng-if="comment.anonymous">Anonymous</em>
              <a ng-if="!comment.anonymous" href="{{ comment.author }}">{{ comment.author }}</a>
            </span>
            <span class="comment-date">{{ comment.date | date: 'medium' }}</span>
          </div>

          <div class="comment-actions">
            <a href="#"></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Comment - Dummy -->
    <div class="comment">
      <!-- Comment Avatar -->
      <div class="comment-avatar">
        <img src="user1.png">
      </div>

      <!-- Comment Box -->
      <div class="comment-box">
        <div class="comment-text">Stress Free is a best place to feel relax.
Really awesome.</div>
        <div class="comment-footer">
          <div class="comment-info">
            <span class="comment-author">
              <a href="mailto:sexar@pagelab.io">Lucy Grey</a>
            </span>
            <span class="comment-date">Dec 26, 2020 9:43:46 PM</span>
          </div>

          <div class="comment-actions">
            <a href="#"></a>
          </div>
        </div>
      </div>
    </div>

    <!-- Comment - Dummy -->
    <div class="comment">
      <!-- Comment Avatar -->
      <div class="comment-avatar">
        <img src="user1.png">
      </div>

      <!-- Comment Box -->
      <div class="comment-box">
        <div class="comment-text">Stress Free can be our little helper to enter a familiar workspace & feel relax.</div>
        <div class="comment-footer">
          <div class="comment-info">
            <span class="comment-author">
              <a href="mailto:ximme13@somedomain.io">Jon Doe</a>
            </span>
            <span class="comment-date">Dec 26, 2020 9:45:29 PM</span>
          </div>

          <div class="comment-actions">
            <a href="#"></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- partial -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.14/angular.min.js'></script><script  src="./script3.js"></script>

</body>
</html>
