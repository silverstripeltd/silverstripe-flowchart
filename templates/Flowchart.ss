<% with $Flowchart %>
    <div class="flowchart" data-flowchart-id="$ID">
        <% loop $Questions %>
            <div class="flowchart__question <% if $Pos != 1 %>flowchart__question--hidden<% end_if %>" data-flowchart-question-id="$ID" <% if not $Responses %>data-flowchart-end="1"<% end_if %>>
                <h3><% if $QuestionHeading %>$QuestionHeading<% else %>Question $Pos<% end_if %></h3>

                <div class="flowchart__question-content">
                    $Content
                    <% if $Info %><a href="#" class="flowchart__question-content-more" title="More info">More info...</a><% end_if %>
                </div>

                <% if $Info %>
                    <div class="flowchart__question-info">
                        $Info
                    </div>
                <% end_if %>

                <% if $Responses %>
                    <div class="flowchart__question-responses">
                        <% loop $Responses %>
                            <button <% if $NextQuestion %>data-next-question-id="$NextQuestion.ID"<% end_if %>>
                                $Label
                            </button>
                        <% end_loop %>
                    </div>
                <% end_if %>

                <% if $hasAnswer %>
                    <div class="flowchart__question-answer">
                        <h3><% if $AnswerHeading %>$AnswerHeading<% else %>Answer<% end_if %></h3>

                        <% if not $AnswerImageAfterContent && $AnswerImage %>
                            <img src="$AnswerImage.FillMax(300, 300).URL" alt="<% if $AnswerHeading %>$AnswerHeading<% else %>Answer<% end_if %>">
                        <% end_if %>

                        $Answer

                        <% if $AnswerImageAfterContent && $AnswerImage %>
                            <img src="$AnswerImage.FillMax(300, 300).URL" alt="<% if $AnswerHeading %>$AnswerHeading<% else %>Answer<% end_if %>">
                        <% end_if %>
                    </div>
                <% end_if %>
            </div>
        <% end_loop %>
    </div>
<% end_with %>

<div class="flowchart__forms">
    <div class="flowchart__form-message"></div>

    <form class="flowchart__form" data-flowchart-id="$Flowchart.ID">
        <% if not $Flowchart.VotingDisabled %>
            <fieldset class="ratings">
                <label>Rating:</label>

                <label class="input-label">
                    1
                    <input type="radio" name="vote" value="1">
                </label>

                <label class="input-label">
                    2
                    <input type="radio" name="vote" value="2">
                </label>

                <label class="input-label">
                    3
                    <input type="radio" name="vote" value="3">
                </label>

                <label class="input-label">
                    4
                    <input type="radio" name="vote" value="4">
                </label>

                <label class="input-label">
                    5
                    <input type="radio" name="vote" value="5">
                </label>
            </fieldset>
        <% end_if %>

        <% if not $Flowchart.FeedbackDisabled %>
            <fieldset class="feedback">
                <label>Feedback:</label>
                <textarea name="feedback"></textarea>
            </fieldset>
        <% end_if %>

        <% if not $Flowchart.FeedbackDisabled || not $Flowchart.VotingDisabled %>
            <input type="hidden" name="securityToken" value="$SecurityToken">
            <button type="submit">Submit</button>
        <% end_if %>
    </form>
</div>
