<?php

namespace App\Notifications;

use App\Models\WorkflowAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $workflowAction;

    public function __construct(WorkflowAction $workflowAction)
    {
        $this->workflowAction = $workflowAction;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $item = $this->workflowAction->item;
        $actionBy = $this->workflowAction->user;
        
        $subject = "Workflow Update: {$item->title}";
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A workflow action has been taken on an item you are involved with.')
            ->line('Item: ' . $item->title)
            ->line('Action: ' . $this->workflowAction->workflowStep->name)
            ->line('By: ' . $actionBy->name)
            ->line('Status: ' . ucfirst($this->workflowAction->status))
            ->line('Comments: ' . ($this->workflowAction->comments ?: 'No comments provided'))
            ->action('View Item', route('items.show', $item))
            ->line('Thank you for using DCMS!');
    }

    public function toArray($notifiable)
    {
        return [
            'workflow_action_id' => $this->workflowAction->id,
            'item_id' => $this->workflowAction->item_id,
            'action' => $this->workflowAction->action,
            'status' => $this->workflowAction->status,
        ];
    }
}