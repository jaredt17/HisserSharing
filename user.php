<?php
/**
 *  User class
 *  Contains all user related functions
 */
class User
{
  public $conn;
  public $userid;
  public $username;
  
  public function __construct($userid) {
    $this->conn = $GLOBALS["conn"];
    $this->userid = $userid;
    $this->initUser($userid);
  }
  
  private function initUser($userid) {
    $sql = "SELECT * FROM Users WHERE userid=".$this->userid;
    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $this->username = $row["username"];
    }
  }
  
  public function getFeed() {
    $followingSql = "SELECT following_userid FROM Followers WHERE follower_userid=".$this->userid;
    $sql = "SELECT * FROM Comments 
            INNER JOIN Users ON Comments.userId = Users.userId 
            WHERE Comments.userid=".$this->userid." OR Comments.userid IN (".$followingSql.") ORDER BY commentdate DESC";
    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        echo '<small>';
        echo '<a href="profile.php?id='.$row["userid"].'">'.$row["firstname"].' '.$row["lastname"].'</a>'; 
        echo ' &middot; '.date("M d", strtotime($row["commentdate"]));
        echo '</small><br />';
        echo $row["comment"]. "<br />";
        echo '<hr />';
      }
    }
  }

  public function getFollowButton() {
    if($_SESSION["userid"] != $this->userid) {
      $sql="SELECT * FROM Followers WHERE follower_userid=" . $_SESSION["userid"] . " AND following_userid=" . $this->userid;

      $result=mysqli_query($this->conn,$sql);
      if(mysqli_num_rows($result)) {
        echo '<input type="submit" id = "followButton" name="unfollow" value="Unfollow">';
      } else {
        echo '<input type="submit" id = "followButton" name="follow" value="Follow">';
      }
    }
  }
  
  public function getComments() {
    $sql = "SELECT * FROM Comments 
            INNER JOIN Users ON Comments.userId = Users.userId 
            WHERE Comments.userid=".$this->userid." ORDER BY commentdate DESC";

    if($_SESSION["userid"] == $this->userid) {
      // include comments from followed users
      echo '<div class="panel-heading">Your feed</div>';
      $followingSql = "SELECT following_userid FROM Followers WHERE follower_userid=".$this->userid;
      $sql = "SELECT * FROM Comments 
              INNER JOIN Users ON Comments.userId = Users.userId 
              WHERE Comments.userid=".$this->userid." OR Comments.userid IN (".$followingSql.") ORDER BY commentdate DESC";
      $result = $this->conn->query($sql);

      if ($result->num_rows > 0) {
        echo '<div class="panel-body" style="word-wrap: break-word">';
        while($row = $result->fetch_assoc()) {
          echo '<small>';
          echo '<a href="profile.php?id='.$row["userid"].'">'."@".$row["username"].' '.'</a>'; 
          echo ' &middot; '.date("M d", strtotime($row["commentdate"]));
          echo '</small><br />';
          echo $row["comment"]. "<br />";
          echo '<hr />';
        }
        echo '</div>';
    }
  }else{
    
    $result = $this->conn->query($sql);
    $top = $result->fetch_assoc();
   
    if ($result->num_rows > 0) {
      echo '<div class = "panel-heading">@'.$top["username"].'\'s Hisses</div>';

      echo '<div class="panel-body" style="word-wrap: break-word">';
      
      while($row = $result->fetch_assoc()) {
        echo '<small>';
        echo '<a href="profile.php?id='.$row["userid"].'">'."@".$row["username"].' '.'</a>'; 
        echo ' &middot; '.date("M d", strtotime($row["commentdate"]));
        echo '</small><br />';
        echo $row["comment"]. "<br />";
        echo '<hr />';
      }

      echo '</div>';

      
      
      
      
      
  }else{
    echo '<p style="font-size: 200%">This User has no posts yet!</p>';
  }

  }
}
  
  public function getOtherUsers() {
    $sql = "SELECT * FROM Users WHERE userid<>" . $this->userid . " AND userid<>" . $_SESSION["userid"];
    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      echo "<ul style='list-style-type:none'>";
      while($row = $result->fetch_assoc()) {
        echo '<li><a href="profile.php?id=' . $row["userid"] . '">' ."@". $row["username"] . " " . '</a></li>';
      }
      echo "</ul>";
    }
  }
  
  public function getNumFollowers() {
    $sql="SELECT * FROM Followers WHERE following_userid=" . $this->userid;
    $result=mysqli_query($this->conn,$sql);
    return mysqli_num_rows($result);
  }
  
  public function getNumFollowing() {
    $sql="SELECT * FROM Followers WHERE follower_userid=" . $this->userid;
    $result=mysqli_query($this->conn,$sql);
    return mysqli_num_rows($result);
  }
  

}
?>