# Articles
Web application to display Wikipedia articles by selected category, ordered by readability score.
It uses ['davechild/textstatistics'](https://github.com/DaveChild/Text-Statistics) to generate a readability score.

## TODOS
- Add unit tests
- Extract main logic, consuming API and generating readability score, from the home action into separate component(s) and inject as dependencies.
- Handle errors that could happend consuming the API. Ex: 500.
- Externalize values to configuration. Ex: API URLs.
- Validate user input.
- Sanitize user input.
- Add style and format to the HTML.
- Create links to Wikipedia for the items in the articles list.
- Display message when error or no articles for category.

## Install

### Prerequisites
- [Docker](https://www.docker.com/products/docker-desktop) and docker compose.
- Internet access.

`composer install`

`docker-compose up`

Go to (http://localhost:8002/)


