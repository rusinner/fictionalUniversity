import $ from "jquery";

class Search {
  //1. describe and create or initiate our object
  constructor() {
    this.addSearchHTML();
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.resultsDiv = $("#search-overlay__results");
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  //2.Events
  events() {
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    $(document).on("keydown", this.keyPressDispatcher.bind(this));
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  //3.Methods (function, action...)
  typingLogic() {
    if (this.searchField.val() !== this.previousValue) {
      clearTimeout(this.typingTimer);

      if (this.searchField.val()) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 500);
      } else {
        this.resultsDiv.html("");
        this.isSpinnerVisible = false;
      }
    }

    this.previousValue = this.searchField.val();
  }

  //at the end of the arrow function there is no need to bind(this) as i would do at a standard function
  //becuase then this keyword would be refered at getJSON method.Now it is being refered at the main object.
  getResults() {
    //we don't use asynchronous when then method because we make one request
    $.getJSON(
      universityData.root_url +
        "/wp-json/university/v1/search?term=" +
        this.searchField.val(),
      (results) => {
        this.resultsDiv.html(`
      <div class="row">

        <div class="one-third">
        <h2 class="search-overlay__section-title">General Information</h2>
        ${
          results.generalInfo.length
            ? '<ul class="link-list min-list">'
            : " <p>No General Information matches this search</p>"
        }
        
        ${results.generalInfo
          .map(
            (item) =>
              ` <li><a href="${item.permalink}"> ${item.title}</a> ${
                item.postType === "post" ? `by ${item.authorName}` : ""
              } </li>`
          )
          .join("")}
       
       ${results.generalInfo.length ? " </ul>" : ""}
        </div>

        <div class="one-third">
        <h2 class="search-overlay__section-title">Programs</h2>
        ${
          results.programs.length
            ? '<ul class="link-list min-list">'
            : ` <p>No programs match this search.<a href="${universityData.root_url}/programs">View all programs</a> </p>`
        }
        
        ${results.programs
          .map(
            (item) =>
              ` <li><a href="${item.permalink}"> ${item.title}</a>
              </li>`
          )
          .join("")}
       
       ${results.programs.length ? " </ul>" : ""}
        
        <h2 class="search-overlay__section-title">Professors</h2>
               ${
                 results.professors.length
                   ? '<ul class="professor-cards">'
                   : ` <p>No professors match this search. </p>`
               }
        
        ${results.professors
          .map(
            (item) =>
              `
              <li class="professor-card__list-item">
                    <a class="professor-card" href="${item.permalink}">
                        <img class="professor-card__image" src="${item.image}" alt="professorLandscape">
                        <span class="professor-card__name">${item.title}</span>
                    </a>
                </li>
              `
          )
          .join("")}
       
       ${results.professors.length ? " </ul>" : ""}
        </div>

        <div class="one-third">
        <h2 class="search-overlay__section-title">Campuses</h2>
        ${
          results.campuses.length
            ? '<ul class="link-list min-list">'
            : ` <p>No campuses match this search. <a href="${universityData.root_url}/campuses">View all campuses</a></p>`
        }
        
        ${results.campuses
          .map(
            (item) =>
              ` <li><a href="${item.permalink}"> ${item.title}</a>
              </li>`
          )
          .join("")}
       
       ${results.campuses.length ? " </ul>" : ""}

        <h2 class="search-overlay__section-title">Events</h2>
        ${
          results.events.length
            ? ""
            : ` <p>No events match this search. <a href="${universityData.root_url}/events">View all events</a></p>`
        }
        
        ${results.events
          .map(
            (item) =>
              `
                <div class="event-summary">
     <a class="event-summary__date t-center" href="${item.permalink}">
         <span class="event-summary__month">${item.month}</span>
         <span class="event-summary__day">${item.day}</span>
     </a>
     <div class="event-summary__content">
         <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
         <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn more</a></p>
     </div>
 </div>
              `
          )
          .join("")}
       </div>
      </div>
      `);
        this.isSpinnerVisible = false;
      }
    );

    //this piece of code in the getResults fuction is been replaced because we made our custom API route
    //run two times getJSON beacuase i want to spread search to all kind of posts and pages
    // $.when(
    //   $.getJSON(
    //     universityData.root_url +
    //       "/wp-json/wp/v2/posts?search=" +
    //       this.searchField.val()
    //   ),
    //   $.getJSON(
    //     universityData.root_url +
    //       "/wp-json/wp/v2/pages?search=" +
    //       this.searchField.val()
    //   )
    // ).then(
    //   (posts, pages) => {
    //     //It is posts[0] and pages[0] because when the promise is being resolved the when then method also returns info about the request
    //     var combinedResults = posts[0].concat(pages[0]);
    //     this.resultsDiv.html(`
    //     <h2 class="search-overlay__section-title">General Information</h2>
    //     ${
    //       combinedResults.length
    //         ? '<ul class="link-list min-list">'
    //         : " <p>No General Information matches this search</p>"
    //     }

    //     ${combinedResults
    //       .map(
    //         (item) =>
    //           ` <li><a href="${item.link}"> ${item.title.rendered}</a> ${
    //             item.type === "post" ? `by ${item.authorName}` : ""
    //           } </li>`
    //       )
    //       .join("")}

    //    ${combinedResults.length ? " </ul>" : ""}
    //     `);
    //     this.isSpinnerVisible = false;
    //   },
    //   () => {
    //     this.resultsDiv.html("<p>Unexpected error please try again!</p>");
    //   }
    // );
  }

  keyPressDispatcher(e) {
    //the third condition inn this if statement is not about this input field but in case
    // we have another one or a text area,prevent s key from opening overlay
    if (
      e.keyCode === 83 &&
      !this.isOverlayOpen &&
      !$("input, textarea").is(":focus")
    ) {
      this.openOverlay();
      console.log("our open method just ran");
    }
    if (e.keyCode === 27 && this.isOverlayOpen) {
      this.closeOverlay();
      console.log("our close method just ran");
    }
  }

  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.searchField.val("");
    //301 ms because this is how it takes to open overlay and i want to trigger focus after that
    setTimeout(() => this.searchField.trigger("focus"), 301);

    this.isOverlayOpen = true;
    //prevent default behavior of a tags and links not to redirect into search page and just open overlay
    return false;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

  addSearchHTML() {
    $("body").append(`
    <div class="search-overlay">
    <div class="search-overlay__top">
        <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term" autocomplete="off">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
        </div>
    </div>
    <div class="container">
        <div id="search-overlay__results">

        </div>
    </div>
</div>
    `);
  }
}

export default Search;
