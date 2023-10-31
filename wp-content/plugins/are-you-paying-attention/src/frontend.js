import "./frontend.scss";

const divsToUpdate = document.querySelectorAll(".paying-attention-upate-me");

//ract render method
divsToUpdate.forEach(function (div) {
  const data = JSON.parse(div.querySelector("pre").innerHTML);
  ReactDOM.render(<Quiz {...data} />, div);
  div.classList.remove("paying-attention-update-me");
});

function Quiz(props) {
  return (
    <div className="paying-attention-frontend">
      <p>{props.question}</p>
      <ul>
        {props.answers.map((answer) => {
          return <li>{answer}</li>;
        })}
      </ul>
    </div>
  );
}
