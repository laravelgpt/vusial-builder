<?php

namespace LaravelBuilder\VisualBuilder\Http\Controllers;

use LaravelBuilder\VisualBuilder\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class TelegramController extends Controller
{
    protected TelegramBotService $telegramService;

    public function __construct(TelegramBotService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle incoming webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        $this->telegramService->handleWebhook($request->all());
        
        return response()->json(['status' => 'ok']);
    }

    /**
     * Set webhook
     */
    public function setWebhook(Request $request): JsonResponse
    {
        $config = $request->validate([
            'token' => 'required|string',
            'webhook_url' => 'required|url',
            'allowed_users' => 'array',
            'backup_notifications' => 'boolean',
            'system_alerts' => 'boolean'
        ]);

        $this->telegramService->setConfig($config);
        $success = $this->telegramService->setWebhook();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Webhook set successfully' : 'Failed to set webhook'
        ]);
    }

    /**
     * Remove webhook
     */
    public function removeWebhook(): JsonResponse
    {
        $success = $this->telegramService->removeWebhook();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Webhook removed successfully' : 'Failed to remove webhook'
        ]);
    }

    /**
     * Get bot information
     */
    public function getBotInfo(): JsonResponse
    {
        $botInfo = $this->telegramService->getBotInfo();

        return response()->json([
            'success' => $botInfo !== null,
            'data' => $botInfo
        ]);
    }

    /**
     * Get webhook information
     */
    public function getWebhookInfo(): JsonResponse
    {
        $webhookInfo = $this->telegramService->getWebhookInfo();

        return response()->json([
            'success' => $webhookInfo !== null,
            'data' => $webhookInfo
        ]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'chat_id' => 'required|integer',
            'message' => 'required|string',
            'options' => 'array'
        ]);

        $success = $this->telegramService->sendMessage(
            $data['chat_id'],
            $data['message'],
            $data['options'] ?? []
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Message sent successfully' : 'Failed to send message'
        ]);
    }

    /**
     * Broadcast message to all allowed users
     */
    public function broadcastMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string',
            'options' => 'array'
        ]);

        $results = $this->telegramService->broadcastMessage(
            $data['message'],
            $data['options'] ?? []
        );

        $successCount = count(array_filter($results));
        $totalCount = count($results);

        return response()->json([
            'success' => $successCount > 0,
            'sent' => $successCount,
            'total' => $totalCount,
            'results' => $results
        ]);
    }

    /**
     * Send backup notification
     */
    public function sendBackupNotification(Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string',
            'details' => 'string'
        ]);

        $results = $this->telegramService->sendBackupNotification(
            $data['status'],
            $data['details'] ?? ''
        );

        return response()->json([
            'success' => !empty($results),
            'results' => $results
        ]);
    }

    /**
     * Send system alert
     */
    public function sendSystemAlert(Request $request): JsonResponse
    {
        $data = $request->validate([
            'alert' => 'required|string',
            'level' => 'string|in:info,warning,error,success'
        ]);

        $results = $this->telegramService->sendSystemAlert(
            $data['alert'],
            $data['level'] ?? 'info'
        );

        return response()->json([
            'success' => !empty($results),
            'results' => $results
        ]);
    }
} 