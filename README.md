# INSTRUCTIONS

This document provides detailed instructions on how to navigate through the functionalities of the Bookworm platform. The platform allows users to explore a book catalogue, view book details, rate and review books, participate in discussion forums, and manage user profiles.

The functionalities are divided into the following sections:
1. Landing Page
2. Navigation
3. Sign-in
4. Sign-up
5. User Profile
6. Book Catalogue
7. Book Details
8. Book Ratings and Reviews
9. Discussion Forums

## Landing Page
#### Functionality

The landing page is the homepage of the platform, providing a brief description and showcasing main features.


| Endpoint | Method |                           |
|----------|--------|---------------------------|
| /        | GET    | Renders the landing page  |

The homepage includes the following sections:

If the user is authenticated:
1. Profile
2. Catalogue
3. Forums
4. Sign Out

If the user is unauthenticated:
1. Sign In
2. Sign Up


## Navigation
### Functionality
The platform must have a consistent navigation menu. The menu will display different links based on the user's authentication status.

- Once the user is authenticated, he can access all the functionalities of the platform. The navigation menu includes links to the book catalogue, discussion forums, and user profile.

- If the user is unauthenticated, the navigation menu will display links to the sign-in and sign-up pages, and if the user trys to access a protected endpoint, he will be redirected to the sign-in page.


## Sign-up
### Functionality
Allows users to create an account. The form includes fields for email, password, and repeat password.

| Endpoint | Method |                          |
|----------|--------|--------------------------|
| /sign-up | GET    | Renders the sign-up page |
| /sign-up | POST   | Handles the sign-up form |

**Validations**
- Email: Required, must be valid, and unique.
- Password: Required, at least 6 characters with at least one number.
- Repeat Password: Must match the password.


**Errors Contemplated**
- The email field is required.
- The email address is not valid.
- The email address is already registered.
- The password must contain at least 6 characters and at least one number.
- The passwords do not match.

## Sign-in
### Functionality
Allows users to log into their account. The form includes fields for email and password.

| Endpoint | Method |                          |
|----------|--------|--------------------------|
| /sign-in | GET    | Renders the sign-in page |
| /sign-in | POST   | Handles the sign-in form |

**Validations**
- Email: Required, must be valid.
- Password: Required.

**Errors Contemplated**
- The email address is not valid.
- The email address or password is incorrect.

## User Profile
### Functionality
Allows authenticated users to view and update their personal information, including email, username, and profile picture.

| Endpoint | Method |                                     |
|----------|--------|-------------------------------------|
| /profile | GET    | Renders the profile page            |
| /profile | POST   | Handles form submission and updates |

**Inputs**
- Email: Pre-filled and disabled.
- Username: Pre-filled and editable.
- Profile Picture: Allows uploading images with specific constraints.

**Validations**
- Profile Picture:
- Size < 1MB
- Formats: PNG, JPG, GIF, SVG
- Dimensions ≤ 400x400 pixels

## Book Catalogue
### Functionality
Displays a list of books and allows authenticated users to add new books via two forms: 
- Normal form (manual input)
- Import form (search by ISBN and fetch book data from OpenLibrary API).

| Endpoint   | Method |                            |
|------------|--------|----------------------------|
| /catalogue | GET    | Renders the catalogue page |
| /catalogue | POST   | Handles book form          |

If the ISBN import input is blank, the form will submit the manual input. If the ISBN import input is filled, the form will fetch book data from the OpenLibrary API.

**Manual Form Inputs**
- Title: Required
- Author: Required
- Description: Required
- Number of Pages: Required
- Cover Image URL: Optional

**ISBN Import Form**
- ISBN: Required

**ISBNs de Proba**

- 9780140328721 - FOX
- 9780393064506 - DRACULA
- 9782743433956 - LITTLE WOMEN
- 9789176370728 - FRANKENSTEIN
- 9780393923209 - METAMORPHOSIS
- 9780060174903 - THE BELL JAR
- 9781943138425 - ANIMAL FARM


## Book Details
### Functionality
Displays detailed information about a selected book, including title, author, description, cover, average rating, and reviews.

| Endpoint        | Method |                       |
|-----------------|--------|-----------------------|
| /catalogue/{id} | GET    | Displays book details |



## Book Ratings and Reviews
### Functionality
Allows authenticated users to rate and review books. Users can submit, update, and delete their ratings and reviews.

| Endpoint               | Method |                            |
|------------------------|--------|----------------------------|
| /catalogue/{id}/rate   | PUT    | Submit or update a rating. |
| /catalogue/{id}/rate   | DELETE | Delete a rating.           |
| /catalogue/{id}/review | PUT    | Submit or update a review. |
| /catalogue/{id}/review | DELETE | Delete a review.           |

## Discussion Forums
### Functionality
Allows authenticated users to participate in book discussions. Includes API endpoints for forum interactions.

| Endpoint         | Method |                                            |
|------------------|--------|--------------------------------------------|
| /forums          | GET    | Displays the forum list.                   |
| /api/forums      | GET    | Retrieves a list of forums in JSON format. |
| /api/forums      | POST   | Creates a new forum.                       |
| /api/forums/{id} | GET    | Retrieves forum details.                   |
| /api/forums/{id} | DELETE | Deletes a forum.                           |



## Forum Posts

This feature is not implemented but this is how it should have worked.

### Functionality
Allows users to view and create posts within forums. Includes API endpoints for post interactions.

| Endpoint               | Method |                                            |
|------------------------|--------|--------------------------------------------|
| /forums/{id}/posts     | GET    | Displays forum details and posts.          |
| /api/forums/{id}/posts | GET    | Retrieves a list of forums in JSON format. |
| /api/forums/{id}/posts | POST   | Creates a new post.                        |
