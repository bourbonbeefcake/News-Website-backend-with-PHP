# News Website backend with PHP

## Features

The website features:

### A password protected administration area that allows the privileged users to:
  1. Add news articles
  2. Add categories
  3. Assign articles to categories
  4. Edit an existing article e.g. changing the title or text
  5. Delete articles from the website
  6. Edit category names
  7. Delete categories

### A publicly visible front-end that allows simple users to:
  1. Browse all the news articles displaying newest first
  2. View a list of news categories in the drop-down menu in the supplied HTML layout
  3. Click on one of the categories to view news articles in that category only
  4. Add a comment to a news article
  5. See comments added to that article by other users. Comments are visible on the news article page, and only comments for the selected article are visible.

### Additional Enhancements:

1. Multi-layered user privileges system with admin, author, user and guest.
2. Moderation of comments. When a comment is added, it&#39;s placed in a holding area in the administration area for administrator approval before appearing on the website
3. Ability to upload an image for an article otherwise the default will appear
4. Social media buttons allowing users to easily share news articles
5. Allow searching news articles by typing in a search text box
6. Allow administrators to manage administrator accounts. Admins are able to create, update, and delete other admin/author users who can then log in and post stories. Stories posted are associated with user who posted them. The news article&#39;s author is visible on the news article&#39;s page. Soft Delete is implemented (meaning the account becomes inactive but still exists in the database with the ability to be restored)
7. Securely store passwords with hashing
8. Users must login to post comments
9. Users are able to reply to a comment effectively allowing nested comments
10. Users can be signed to the newsletter and receive notifications whenever a new article is posted (non-functional as it needs a mail server but implemented in code)
11. The ability to click on a user and see any comment they have made
12. The ability to see all news articles posted by a specific author

### **As a note** : Little to no attention was given to the graphical design of the website as this assignment is purely about the backend of the website.

## Database Design
![](https://github.com/antoniosTriant/News-Website-backend-with-PHP/blob/master/documentation/images/erd.png)

## Technical Documentation

**layout.php** is the skeleton of the website, containing the HTML elements for every area. It is required on the end of every other page that needs the graphical interface in order to be displayed correctly. Some of its sections are in separate files and then required in it:

- **navBar.php** – Implements the navigation bar
- **sidebar.php** – Implements the side bar, and is required only if there is a user logged in the website.

There are also two variables in it that are changed dynamically depending on the needs of each page that requires layout.php:

- **content** – the content of the page
- **title** – the title of the page displayed on the browser tab

The search field is also in the layout to be available on every page that the user visits.

**style.css** is also added in this file&#39;s head for the styling of all elements.

A file named **db\_config.php,** establishes connection with the database. It keeps that connection into a PDO instance. This file is required to every page that needs to interact with the database.

Furthermore, there are four files that contain functions and queries for a specific entity:

- **userQueries.class.php** – Implements the &quot;User&quot; class and user-related functions and queries.
- **categoryQueries.class.php** – Implements the &quot;categoryQueries&quot; class and category-related functions and queries.
- **articleQueries.class.php** - Implements the &quot;articleQueries&quot; class and article-related functions and queries.
- **commentQueries.class.php** -  Implements the &quot;commentQueries&quot; class and comment-related functions and queries.

Each of these classes are instantiated in **db\_config.php** and the PDO passes as an argument to the constructor of each class. Having entity-specific functions and queries in one file organizes the code and makes it easier to find.

This was done so that, all these objects with all the useful functions and queries to be available to the file that required **db\_config.php**.

There are also files that implement classes that are used for filling the instance with data from the database. Each of these classes implement setter and getter functions to manipulate the data inserted.

- **article.class.php** – article specific attributes contained
- **comment.class.php** – comment specific attributes contained

The reasoning behind the implementation of such files, is that whenever it is needed to store data about an article or comment from the database to process it, it is easier and more organized to create an instance and use its getter and setter functions to display or alter the data, before saving it back to the database, rather than creating separate variables or associative arrays.

There are three user roles and each grants certain privileges. In the following numbered list, the number for each role is not random, as it is used as a PRIMARY KEY in the database.

1. **Admin** - The administrator of the website, the role with the most privileges
2. **Author** – Authors are more focused around posting and editing articles
3. **User** – The simple registered user.

**login.php**

Can view content: Anyone

Shows the login form and calls appropriate functions to start a new session keeping the logged-in user&#39;s ID, if every check is passed.

**logout.php**

Can view content: Anyone

Unsets and destroys the current session.

**register.php**

Can view content: Non-registered users

Shows the register form, makes appropriate checks to validate user input and calls appropriate functions to register the user into the database.

**userProfile.php**

Can view content: Registered users, authors, admins

Registered user can see basic details about other users, display articles posted by authors and admins, comments by other users. They can also see their own profile and change their email, username, newsletter status. They can also delete their account.

Administrators can also change a user&#39;s role and restore a deleted user.

**userComments.php**

Can view content: Registered users, authors, admins

Lists all the comments of a user or a message if there are not any. The ID of this user is set in the $\_GET superglobal when someone requests this page by clicking on the relevant link.

**userArticles.php**

Can view content: Registered users, authors, admins

Only admins and authors can post articles. This page lists all the articles posted by a specific user. The ID of this user is set in the $\_GET superglobal when someone requests this page by clicking on the relevant link.

**deleteUser.php**

Can view content: admins

Deletion of a user is not deletion of the user&#39;s record in the database. The &quot;is\_deleted&quot; user attribute takes value &quot;y&quot; if the user is deleted and &quot;n&quot; otherwise. If this attribute has the &quot;y&quot; value, the user cannot login, and articles or/and comments posted, show the user&#39;s name with a strikethrough. Only the admin can restore the deleted user. This ensures that the database keeps records and makes it much easier to manipulate user assets (articles, comments) instead of deleting them and easier to restore. \*\*

The user&#39;s ID is supplied by $\_GET superglobal. If the requested user exists and the current user is permitted for this action, then the attribute &quot;is\_deleted&quot; of the requested user is updated to &quot;y&quot;.

**restoreUser.php**

Can view content: admins

The user&#39;s ID is supplied by $\_GET superglobal. If the requested user exists and the current user is permitted for this action, then the attribute &quot;is\_deleted&quot; of the requested user is updated to &quot;n&quot;.

**manageUsers.php**

Can view content: admins

Indexes all user records from the database in a table. There is a link provided for each that redirects to the user&#39;s full profile.

**editUserDetails.php**

Can view content: the registered user that updates his profile, admins

The user&#39;s ID is supplied by $\_GET superglobal. When a user updates their profile, is redirected to this page. This page validates the inserted data. The criteria for the update to succeed are explained below:

- The user-name if not empty, must, be unique, be smaller than 20 characters, start with a letter and contain only numbers and letters.
- The email if not empty, must be unique and be of a valid format (local-part@domain).

If any of the fields is empty, then no changes apply on that field.

The role changes to the one selected, if an admin initiated the update.

**latestArticles.php**

Can view content: Anyone

Displays all articles by descending order, meaning from newest to oldest. The articles are not shown fully. The user must click on the title of the article or the &quot;Read More&quot; link to open the full article.

**article.php**

Can view content: Anyone

Displays an article&#39;s full content, options, comments, and social sharing buttons, if it exists. The ID of the article to be displayed comes from the $\_GET superglobal.

If the author of the article or the admin visits the article page, there are options to delete it, change its title, the category or its content. Additionally, the options to delete comments is available.

**addArticle.php**

Can view content: Authors, Admins

A new article must fulfil the criteria below to be added:

- The image must be of a valid format (.png, jpg) and be smaller than 15MB.
- The title cannot be blank, must be unique and be smaller than 40 characters.
- The content cannot be blank, and be larger than 100 characters.
- Be assigned to a category

If there is no image uploaded, the system uses the default image. (images/default-no-image-icon.png)

If the user did not meet the criteria when submitting the form, the form keeps the user&#39;s input and displays the errors occurred. This was done because it would be inconvenient for the user to retype any long content that he already had typed.

Once the criteria are met, the article is inserted into the database, and a mail is send to all users subscribed to the newsletter informing them of the new article post.

**deleteArticle.php**

Can view content: The author of the article, Admins

The ID of the article to be deleted is retrieved by $\_GET superglobal. If the article that is requested exists, then it is deleted from the database. When an article is deleted, the database deletes all its comments as well. (ON DELETE: CASCADE)

**manageArticles.php**

Can view content: authors, Admins

Indexes all article records from the database in a table. There is a link provided for each that redirects to the article&#39;s full profile. Authors can only view the articles that they posted. Admins can see all articles.



**approveComments.php**

Can view content: authors, Admins

Lists all comments that are pending approval. Provides links to approve or delete a comment. Comment approval is not checked in a different page.

Each user, author or admin, views comments relevant for his articles only, to share the workload equally.

**deleteComment.php**

Can view content: the user who posted the comment, the commented article&#39;s author, admins

Both article and comments IDs are supplied by $\_GET superglobal. If the requested comment and article exist, the comment is deleted from the database.

Note: If an author or an admin reply to or post a comment to an article ,the comment is automatically approved.

**comment\_Reply.php**

Can view content: registered users, authors, Admins

Handles the commenting of a comment.

**commentAnArticle.php**

Can view content: registered users, authors, Admins

Handles the commenting of an article.

Criteria about category name: The name must not exceed 20 characters in length,cannot be blank and be unique among the other categories.

**addCategory.php**

Can view content: authors, Admins

Inserts a new category in the database if it meets the criteria.

**deleteCategory.php**

Can view content: Admins

A dropdown menu allows the admin to select the category for deletion. When the Delete button is clicked, the category is deleted from the database.

**editCategoryName.php**

Can view content: Admins

Dropdown menu lets the admin select a category to rename, and the text field to type in the new name. When submitted the category is renamed if the name meets the criteria.

**category.php**

Can view content: anyone

This page gets the ID of the category that the user selected from the $\_GET superglobal, and displays all the articles under this category in descending order.

**search.php**

Can view content: anyone

After a user performs a query in the search field, he is redirected to this page, where articles that in their title contain part of the string he typed are indexed. Otherwise this page informs the user that there were no results.

**index.php**

The home page.
