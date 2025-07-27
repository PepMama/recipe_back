<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Firebase\JWT\JWT;

#[Route('/user')]
class UserController extends AbstractController
{
    /**
     *  Creates a new user.
     *
     * @return JsonResponse with token and role
     * */
    #[Route('/create-user', name:'app_create_user', methods:  ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try{
            $data = json_decode($request->getContent(), true);

            // Vérifier que les données requises sont présentes
            if (!isset($data['email']) || !isset($data['firstname']) || !isset($data['lastname']) || !isset($data['password'])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            // Verify if the user.email does not already exist
            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if($existingUser)
            {
                return new JsonResponse(['error' => 'User with this email already exists.'], Response::HTTP_CONFLICT);
            }

            // Add user to the bdd
            $User = new User();
            $User->setEmail($data['email']);
            $User->setFirstname($data['firstname']);
            $User->setLastname($data['lastname']);
            $User->setCreatedAt(new \DateTimeImmutable());

            $roles = [];
            if (isset($data['roles'])) {
                if (is_array($data['roles'])) {
                    $roles = $data['roles'];
                } else {
                    $roles = [$data['roles']];
                }
            }
            $User->setRoles($roles);
            
            $hashedPassword = $passwordHasher->hashPassword($User, $data['password']);
            $User->setPassword($hashedPassword);

            $em->persist($User);
            $em->flush();

            // Create the token 
            $jwtKey = $this->getParameter('jwt_key');
            $payload = [
                'user_id' => $User->getId(),
                'email' => $User->getEmail(),
                'role' => $User->getRoles()[0] ?? 'ROLE_USER',
            ];
            $jwt = JWT::encode($payload, $jwtKey, 'HS256');

            // Return the token and the role in the response
            return new JsonResponse([
                'token' => $jwt,
                'role' => $User->getRoles()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try{
            $data = json_decode($request->getContent(), true);

            $email = $data['email'];
            $password = $data['password'];

            $User = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if(!$User || !$passwordHasher->isPasswordValid($User, $password)) {
                return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }

            $jwtKey = $this->getParameter('jwt_key');
            $payload = [
                'user_id' => $User->getId(),
                'email' => $User->getEmail(),
                'role' => $User->getRoles()[0] ?? 'ROLE_USER',
            ];
            $jwt = JWT::encode($payload, $jwtKey, 'HS256');

            // Return the token and the role in the response
            return new JsonResponse([
                'token' => $jwt,
                'role' => $User->getRoles()
            ]);
        }catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
