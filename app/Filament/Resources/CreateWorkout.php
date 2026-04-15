<?php

namespace App\Filament\Resources\WorkoutResource\Pages;

use App\Filament\Resources\WorkoutResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
use Filament\Forms;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class CreateWorkout extends CreateRecord
{
    protected static string $resource = WorkoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('gerar_treino_ia')
                ->label('Gerar Treino (IA)')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('ai_level')
                        ->label('Nível da Turma')
                        ->options([
                            'Iniciante' => 'Iniciante',
                            'Intermediário' => 'Intermediário',
                            'Avançado' => 'Avançado',
                            'RX' => 'RX',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('ai_technique')
                        ->label('Foco / Técnica do Dia')
                        ->placeholder('Ex: Snatch, Ginástico, LPO, Cardio...')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $apiKey = env('GEMINI_API_KEY');

                    if (!$apiKey) {
                        Notification::make()
                            ->title('Chave de API não configurada')
                            ->body('Adicione a GEMINI_API_KEY no seu arquivo .env')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Definimos um formato estrito para a IA nos retornar para fácil extração
                    $prompt = "Você é um Head Coach de CrossFit experiente. Crie um treino do dia para uma turma de nível '{$data['ai_level']}' com o foco técnico principal em '{$data['ai_technique']}'.\n\n"
                        . "Sua resposta DEVE obedecer rigorosamente o formato abaixo, sem saudações ou markdown de blocos de código:\n\n"
                        . "[TIPO_WOD]\nAMRAP (ou EMOM, For Time, Tabata, Chipper, Cindy, Relay, Outro)\n\n"
                        . "[SKILL]\nDetalhes do aquecimento e técnica do dia aqui, em formato HTML básico (usando <ul>, <li>, <strong>, <br>).\n\n"
                        . "[WOD]\nDescrição completa do WOD sugerido aqui, em formato HTML básico (usando <ul>, <li>, <strong>, <br>).";

                    try {
                        $response = Http::retry(3, 2000)->withHeaders([
                            'Content-Type' => 'application/json',
                        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ]
                        ]);

                        if ($response->successful()) {
                            $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                            
                            // Extração baseada nas tags geradas pela IA
                            preg_match('/\[TIPO_WOD\](.*?)(?=\[SKILL\])/s', $text, $tipoMatches);
                            preg_match('/\[SKILL\](.*?)(?=\[WOD\])/s', $text, $skillMatches);
                            preg_match('/\[WOD\](.*)/s', $text, $wodMatches);

                            // Preenche os dados no formulário da tela principal (Create Record)
                            $this->data['level'] = $data['ai_level'];
                            $this->data['wod_type'] = isset($tipoMatches[1]) ? trim($tipoMatches[1]) : 'Outro';
                            $this->data['skill'] = isset($skillMatches[1]) ? trim($skillMatches[1]) : '';
                            $this->data['wod_description'] = isset($wodMatches[1]) ? trim($wodMatches[1]) : $text;

                            Notification::make()->title('Treino Gerado com Sucesso!')->success()->send();
                        } else {
                            Notification::make()->title('Erro ao contatar IA.')->body($response->body())->danger()->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Erro Inesperado.')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}