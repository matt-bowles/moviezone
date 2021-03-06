<?php
require_once("moviezone_main.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MovieZone</title>

    <!--    Local CSS   -->
    <link href="css/stylesheet.css" rel="stylesheet">

</head>
<body>
<div id="wrapper">
    <header>
        <h1 id="heading">MovieZone</h1>
        <?php $view->topNavbar() ?>
    </header>

    <main>
        <div id="leftPanel">
            <h2>New Releases</h2>
            <?php $controller->loadNewReleasesLeftPanel()?>
        </div>

        <div id="rightPanel">
            <?php $controller->checkLoggedIn()?>
            <h1>TechZone</h1>

          <div id="it_experts">
            <h4>Our Store IT Expert</h4>
            <img src="img/bill_in_suit.jpg" alt="Our Tech Team" height="150" width="153" />
            <p>Dr. Smart</p>
          </div>

          <div id="article_select_box">
            <h5 style="color: white">Articles</h5>
            <ul>
              <li><a href="#article1">What CMS does your business need?</a></li>
              <li><a href="#article2">Which web server is best for you?</a></li>
            </ul>
          </div>
          <br>

          <div class="article">
          <h3 id="article1">What CMS does your business need?</h3>
            <p>August 2015</p>

            <div class="article_content">
              <p>While it is easy to develop a static web site for your business the task of developing a site that can handle dynamic content such as blog posts, social networking, ecommerce, and customer support is much harder. It would also be very expensive to develop such software in-house. This is why many businesses use a content management system to drive their websites.</p>

              <p>A content management system is software which takes content stored in a database and transforms it for display as a web page. They allow non-technical staff to make updates through web based editing forms, with no knowledge required as to how the system actually stores or transforms the content. In addition the presentation of your content (such as the web site theme) can be changed without having to manually update a large number of web pages. Apart from their value in managing simple web site content these systems can be extended to handle tasks like ecommerce, customer relationship management, supply chain management, and enterprise resource planning.</p>

              <p>In this article we will look at four CMS systems Wordpress, XXXX, XXX, and XXXX comparing and contrasting their features and pricing.</p>

              <p>Wordpress is the web???s most popular content management system, with an estimated 60% of the CMS market (w3techs.com 2014). Wordpress was created in 2003 and was originally designed to be a blogging platform (Tiwari 2014). Development over the years has expanded it into a general purpose CMS (Calao 2012). </p>

              <p>Much more required for this article ......</p>
              <p>Word count: XXXX</p>
            </div>

            <h5>References</h5>
            <p>Colao, J., 2012. With 60 Million Websites, WordPress Rules The Web. So Where's The Money?. [online] Forbes. Available at: <http://www.forbes.com/sites/jjcolao/2012/09/05/the-internets-mother-tongue/> [Accessed 11 Jan. 2015].</p>
            <p>Tiwari, N., 2014. Which content management system is right for you? | Opensource.com. [online] Opensource.com. Available at: <http://opensource.com/business/14/6/open-source-cms-joomla-wordpress-drupal> [Accessed 11 Jan. 2015].</p>
            <p>W3techs.com, 2014. Usage Statistics and Market Share of Content Management Systems for Websites, January 2015. [online] Available at: <http://w3techs.com/technologies/overview/content_management/all> [Accessed 11 Jan. 2015].</p>
          </div>
          <br>
          <br>
          <div class="article">
            <h3 id="article2">Which web server is best for you?</h3>
            <p>June 2015</p>
            <div class="article_content">
              <p>When it comes to choosing a web server for hosting a business site the major choice is between the Apache web server and Microsoft???s Internet Information Services web server (Sebesta 2014). Both of these servers data back to the birth of the web in the mid-90s and have for much of that time been the major players in the web server market. In this essay I will first look at the Apache web servers history and capabilities, then the history and capability of Internet Information Services, followed by a look at the possible costs for using both servers, and will conclude with my recommendation on how to choose between the two.</p>

              <p>The Apache web server (hereafter Apache) started in 1995 as a continuation of the NCSA (National Center for Supercomputing Applications) web server (Apache Software Foundation 2014) at the time the most popular web server in use (Netcraft 2014a). Apache is free software and distributed under a license which allows not only free use of the software but for modification and redistribution of the source code, even for commercial purposes. The Apache project is run by the non-profit Apache Software Foundation. As of November 2014 Apache is still the most popular web server with 50.01% of active web sites being hosted on Apache (Netcraft 2014b).</p>

              <p>Much more required for this article ......</p>
              <p>Word count: XXXX</p>
            </div>

            <h5>References</h5>
            <p>Apache Software Foundation, 2014. About the Apache HTTP Server Project - The Apache HTTP Server Project. httpd.apache.org. Available at: <https://httpd.apache.org/ABOUT_APACHE.html> [Accessed 25 Jan. 2015].</p>
            <p>Netcraft, 2014a. November 2014 Web Server Survey | Netcraft. News.netcraft.com. Available at: <http://news.netcraft.com/archives/2014/11/19/november-2014-web-server-survey.html#more-17712> [Accessed 29 Jan. 2015].</p>
            <p>Netcraft, 2014b. Web Server Survey Turns 10, Finds 70 Million Sites | Netcraft. News.netcraft.com. Available at: <http://news.netcraft.com/archives/2005/08/01/web_server_survey_turns_10_finds_70_million_sites.html> [Accessed 29 Nov. 2014].</p>
            <p>Sebesta, R.W., 2014. "Programming the World Wide Web", 8th ed., Pearson, New Jersey U.S.A.</p>
          </div>
        </div>
    </main>

  <?php $view->footer(); ?>
</div>

</body>
</html>